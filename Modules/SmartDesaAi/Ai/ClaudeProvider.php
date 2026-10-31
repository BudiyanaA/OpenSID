<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

use Anthropic\Client;
use Anthropic\Core\Exceptions\AnthropicException;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\BadRequestException;
use Anthropic\Core\Exceptions\NotFoundException;
use Anthropic\Core\Exceptions\PermissionDeniedException;
use Anthropic\Core\Exceptions\RateLimitException;
use Anthropic\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Throwable;

/**
 * Penyedia Claude (Anthropic) memakai SDK resmi anthropic-ai/sdk.
 */
final class ClaudeProvider implements AiProviderInterface
{
    public const KODE = 'claude';

    /** Model bawaan bila desa belum memilih. */
    public const MODEL_BAWAAN = 'claude-opus-5';

    /** Model yang mendukung server-side refusal fallback (beta). */
    private const MODEL_DENGAN_FALLBACK = ['claude-opus-5', 'claude-fable-5-1'];

    private Client $client;

    public function __construct(string $token, float $timeout = 120.0)
    {
        if ('' === trim($token)) {
            // Jangan pernah membuat client dengan token kosong: SDK akan
            // mencari kredensial lain di environment server.
            throw new AiProviderException(AiProviderException::TOKEN_KOSONG);
        }

        $factory = new HttpFactory();

        $this->client = new Client(
            apiKey: $token,
            authToken: '',
            // Dikunci agar env ANTHROPIC_BASE_URL di server tidak dapat
            // mengalihkan token desa ke host lain.
            baseUrl: 'https://api.anthropic.com',
            requestOptions: RequestOptions::with(
                timeout: $timeout,
                maxRetries: 1,
                // Timeout ditegakkan oleh transport, bukan oleh SDK.
                transporter: new GuzzleClient(['timeout' => $timeout, 'connect_timeout' => 10]),
                uriFactory: $factory,
                streamFactory: $factory,
                requestFactory: $factory,
            ),
        );
    }

    public function kode(): string
    {
        return self::KODE;
    }

    public function generate(string $model, string $system, string $prompt, array $schema, int $maxTokens): AiResult
    {
        $model = $model ?: self::MODEL_BAWAAN;

        $params = [
            'model'        => $model,
            'maxTokens'    => $maxTokens,
            'system'       => $system,
            'messages'     => [['role' => 'user', 'content' => $prompt]],
            'outputConfig' => ['format' => ['type' => 'json_schema', 'schema' => $schema]],
        ];

        $pesan = $this->panggil(function () use ($model, $params) {
            if (in_array($model, self::MODEL_DENGAN_FALLBACK, true)) {
                // Bila model menolak (refusal), API menjalankan ulang di model
                // cadangan dalam panggilan yang sama.
                return $this->client->beta->messages->create(
                    ...$params,
                    fallbacks: 'default',
                    betas: ['server-side-fallback-2026-07-01'],
                );
            }

            return $this->client->messages->create(...$params);
        });

        if ('refusal' === $pesan->stopReason) {
            throw new AiProviderException(AiProviderException::DITOLAK_MODEL);
        }

        if ('max_tokens' === $pesan->stopReason) {
            throw new AiProviderException(AiProviderException::TERPOTONG);
        }

        $teks = '';

        foreach ($pesan->content as $blok) {
            if ('text' === $blok->type) {
                $teks .= $blok->text;
            }
        }

        return new AiResult(
            text: $teks,
            model: (string) ($pesan->model ?? $model),
            inputTokens: $pesan->usage->inputTokens ?? null,
            outputTokens: $pesan->usage->outputTokens ?? null,
        );
    }

    public function listModels(): array
    {
        $halaman = $this->panggil(fn () => $this->client->models->list(limit: 100));

        $model = [];

        foreach ($halaman->getItems() as $info) {
            $model[] = $info->id;
        }

        return $model;
    }

    /**
     * Jalankan panggilan SDK dan ubah semua kesalahan menjadi
     * AiProviderException yang aman (tanpa request/header/token).
     */
    private function panggil(callable $panggilan)
    {
        try {
            return $panggilan();
        } catch (AuthenticationException) {
            throw new AiProviderException(AiProviderException::TOKEN_INVALID, 401);
        } catch (PermissionDeniedException) {
            throw new AiProviderException(AiProviderException::AKSES_DITOLAK, 403);
        } catch (NotFoundException) {
            throw new AiProviderException(AiProviderException::MODEL_INVALID, 404);
        } catch (RateLimitException) {
            throw new AiProviderException(AiProviderException::KUOTA, 429);
        } catch (BadRequestException $e) {
            throw AiProviderException::dariStatus($e->status ?? 400);
        } catch (APIStatusException $e) {
            throw AiProviderException::dariStatus($e->status);
        } catch (APIConnectionException) {
            throw new AiProviderException(AiProviderException::KONEKSI);
        } catch (AiProviderException $e) {
            throw $e;
        } catch (AnthropicException|Throwable) {
            throw new AiProviderException(AiProviderException::TIDAK_DIKENAL);
        }
    }
}
