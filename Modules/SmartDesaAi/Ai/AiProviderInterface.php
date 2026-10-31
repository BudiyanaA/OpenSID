<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

/**
 * Kontrak penyedia AI. Fitur generator artikel hanya bergantung pada
 * interface ini, sehingga penyedia (Claude, OpenAI, dst.) dapat diganti
 * lewat pengaturan tanpa mengubah kode generator.
 *
 * Implementasi WAJIB:
 * - menerima token hanya lewat konstruktor (tidak membaca env server),
 * - tidak pernah menulis token/prompt ke log,
 * - melempar AiProviderException dengan pesan yang aman ditampilkan.
 */
interface AiProviderInterface
{
    /**
     * Kode penyedia yang disimpan di pengaturan (mis. "claude", "openai").
     */
    public function kode(): string;

    /**
     * Minta teks terstruktur (JSON sesuai $schema) dari model.
     *
     * @param array<string, mixed> $schema JSON Schema objek hasil
     *
     * @throws AiProviderException
     */
    public function generate(string $model, string $system, string $prompt, array $schema, int $maxTokens): AiResult;

    /**
     * Daftar ID model yang tersedia untuk token ini. Sekaligus berfungsi
     * sebagai uji koneksi (token tidak valid -> AiProviderException).
     *
     * @return list<string>
     *
     * @throws AiProviderException
     */
    public function listModels(): array;
}
