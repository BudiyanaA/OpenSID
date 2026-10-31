<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

use InvalidArgumentException;

/**
 * Membuat penyedia AI berdasarkan kode di pengaturan instalasi desa.
 * Menambah penyedia baru cukup dengan menambah entri di PENYEDIA.
 */
final class AiProviderFactory
{
    /** @var array<string, array{label: string, kelas: class-string<AiProviderInterface>, model_bawaan: string}> */
    public const PENYEDIA = [
        ClaudeProvider::KODE => ['label' => 'Claude (Anthropic)', 'kelas' => ClaudeProvider::class, 'model_bawaan' => ClaudeProvider::MODEL_BAWAAN],
        OpenAiProvider::KODE => ['label' => 'OpenAI', 'kelas' => OpenAiProvider::class, 'model_bawaan' => ''],
    ];

    public static function valid(?string $kode): bool
    {
        return is_string($kode) && isset(self::PENYEDIA[$kode]);
    }

    /**
     * @return array<string, string> kode => label
     */
    public static function pilihan(): array
    {
        return array_map(static fn ($p) => $p['label'], self::PENYEDIA);
    }

    public static function modelBawaan(string $kode): string
    {
        return self::PENYEDIA[$kode]['model_bawaan'] ?? '';
    }

    /**
     * @throws AiProviderException bila token kosong
     */
    public static function buat(string $kode, string $token, float $timeout = 120.0): AiProviderInterface
    {
        if (! self::valid($kode)) {
            throw new InvalidArgumentException('Penyedia AI tidak dikenal.');
        }

        $kelas = self::PENYEDIA[$kode]['kelas'];

        return new $kelas($token, $timeout);
    }
}
