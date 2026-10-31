<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Services;

use Modules\SmartDesaAi\Models\SmartDesaAiLog;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Batas pemakaian generate (rate limit) berdasarkan log pemakaian:
 * - per pengguna per jam,
 * - per hari untuk seluruh instalasi desa.
 * Nilai batas diambil dari pengaturan agar mudah diubah.
 */
class BatasPemakaian
{
    public function __construct(private readonly Pengaturan $pengaturan)
    {
    }

    public function pemakaianUserSejamTerakhir(int $userId): int
    {
        return SmartDesaAiLog::generate()
            ->where('user_id', $userId)
            ->where('created_at', '>=', date('Y-m-d H:i:s', time() - 3600))
            ->count();
    }

    public function pemakaianHariIni(): int
    {
        return SmartDesaAiLog::generate()
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->count();
    }

    /**
     * Pesan penolakan bila batas tercapai, atau null bila masih boleh.
     */
    public function cek(int $userId): ?string
    {
        if ($this->pemakaianHariIni() >= $this->pengaturan->batasHarian()) {
            return sprintf('Batas harian Smart Desa AI (%d generate/hari untuk desa ini) sudah tercapai. Coba lagi besok.', $this->pengaturan->batasHarian());
        }

        if ($this->pemakaianUserSejamTerakhir($userId) >= $this->pengaturan->batasPerUserPerJam()) {
            return sprintf('Anda sudah mencapai batas %d generate per jam. Coba lagi beberapa saat lagi.', $this->pengaturan->batasPerUserPerJam());
        }

        return null;
    }

    /**
     * Ringkasan untuk ditampilkan di halaman.
     *
     * @return array{hari_ini: int, batas_harian: int, user_jam: int, batas_user_jam: int}
     */
    public function ringkasan(int $userId): array
    {
        return [
            'hari_ini'       => $this->pemakaianHariIni(),
            'batas_harian'   => $this->pengaturan->batasHarian(),
            'user_jam'       => $this->pemakaianUserSejamTerakhir($userId),
            'batas_user_jam' => $this->pengaturan->batasPerUserPerJam(),
        ];
    }
}
