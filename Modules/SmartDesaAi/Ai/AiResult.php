<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

/**
 * Hasil panggilan penyedia AI: teks JSON mentah + metadata pemakaian.
 */
final class AiResult
{
    public function __construct(
        public readonly string $text,
        public readonly string $model,
        public readonly ?int $inputTokens = null,
        public readonly ?int $outputTokens = null,
    ) {
    }
}
