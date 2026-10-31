<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Throwable;

/**
 * Penyedia OpenAI (Chat Completions + Structured Outputs), lewat HTTP.
 */
final class OpenAiProvider implements AiProviderInterface
{
    public const KODE = 'openai';

    private const BASE_URL = 'https://api.openai.com/v1/';

    private GuzzleClient $http;

    public function __construct(string $token, float $timeout = 120.0)
    {
        if ('' === trim($token)) {
            throw new AiProviderException(AiProviderException::TOKEN_KOSONG);
        }

        $this->http = new GuzzleClient([
            'base_uri'        => self::BASE_URL,
            'timeout'         => $timeout,
            'connect_timeout' => 10,
            'http_errors'     => false,
            'headers'         => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function kode(): string
    {
        return self::KODE;
    }

    public function generate(string $model, string $system, string $prompt, array $schema, int $maxTokens): AiResult
    {
        if ('' === $model) {
            throw new AiProviderException(AiProviderException::MODEL_INVALID);
        }

        $data = $this->kirim('POST', 'chat/completions', [
            'model'                 => $model,
            'max_completion_tokens' => $maxTokens,
            'messages'              => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => [
                'type'        => 'json_schema',
                'json_schema' => ['name' => 'artikel_desa', 'strict' => true, 'schema' => $schema],
            ],
        ]);

        $pilihan = $data['choices'][0] ?? null;

        if (! $pilihan) {
            throw new AiProviderException(AiProviderException::FORMAT);
        }

        if (! empty($pilihan['message']['refusal'])) {
            throw new AiProviderException(AiProviderException::DITOLAK_MODEL);
        }

        if ('length' === ($pilihan['finish_reason'] ?? null)) {
            throw new AiProviderException(AiProviderException::TERPOTONG);
        }

        return new AiResult(
            text: (string) ($pilihan['message']['content'] ?? ''),
            model: (string) ($data['model'] ?? $model),
            inputTokens: isset($data['usage']['prompt_tokens']) ? (int) $data['usage']['prompt_tokens'] : null,
            outputTokens: isset($data['usage']['completion_tokens']) ? (int) $data['usage']['completion_tokens'] : null,
        );
    }

    public function listModels(): array
    {
        $data = $this->kirim('GET', 'models');

        return collect($data['data'] ?? [])
            ->pluck('id')
            ->filter(static fn ($id) => is_string($id) && preg_match('/^(gpt-|o\d|chatgpt-)/', $id)
                && ! preg_match('/(audio|realtime|transcribe|tts|image|embedding|search|moderation|instruct)/', $id))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function kirim(string $metode, string $uri, ?array $body = null): array
    {
        try {
            $respons = $this->http->request($metode, $uri, null === $body ? [] : ['json' => $body]);
        } catch (ConnectException) {
            throw new AiProviderException(AiProviderException::KONEKSI);
        } catch (RequestException|Throwable) {
            // Pesan exception Guzzle bisa memuat potongan request; jangan diteruskan.
            throw new AiProviderException(AiProviderException::KONEKSI);
        }

        $status = $respons->getStatusCode();

        if ($status >= 400) {
            // Isi error OpenAI dapat memuat potongan token (mis. "Incorrect API
            // key provided: sk-...") sehingga hanya status yang dipakai.
            throw AiProviderException::dariStatus($status);
        }

        $data = json_decode((string) $respons->getBody(), true);

        if (! is_array($data)) {
            throw new AiProviderException(AiProviderException::FORMAT);
        }

        return $data;
    }
}
