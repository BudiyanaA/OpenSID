<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Ai;

use RuntimeException;
use Throwable;

/**
 * Kesalahan dari penyedia AI.
 *
 * getMessage() SELALU pesan berbahasa Indonesia yang aman ditampilkan ke
 * pengguna: tidak memuat token, isi respons mentah, atau detail internal.
 * $kode adalah kategori singkat untuk log pemakaian (tanpa data sensitif).
 */
final class AiProviderException extends RuntimeException
{
    public const TOKEN_KOSONG   = 'token_kosong';
    public const TOKEN_INVALID  = 'token_invalid';
    public const AKSES_DITOLAK  = 'akses_ditolak';
    public const MODEL_INVALID  = 'model_invalid';
    public const KUOTA          = 'kuota_atau_rate_limit';
    public const SIBUK          = 'layanan_sibuk';
    public const KONEKSI        = 'koneksi_gagal';
    public const DITOLAK_MODEL  = 'ditolak_model';
    public const TERPOTONG      = 'hasil_terpotong';
    public const FORMAT         = 'format_tidak_valid';
    public const PERMINTAAN     = 'permintaan_tidak_valid';
    public const TIDAK_DIKENAL  = 'tidak_dikenal';

    private const PESAN = [
        self::TOKEN_KOSONG  => 'Token API belum diatur. Buka Pengaturan → Smart Desa AI.',
        self::TOKEN_INVALID => 'Token API ditolak oleh penyedia AI. Periksa kembali token di Pengaturan → Smart Desa AI.',
        self::AKSES_DITOLAK => 'Token API tidak memiliki izin untuk permintaan ini.',
        self::MODEL_INVALID => 'Model yang dipilih tidak tersedia untuk token ini. Pilih model lain di pengaturan.',
        self::KUOTA         => 'Batas pemakaian atau kuota akun penyedia AI tercapai. Coba lagi nanti atau periksa saldo akun API desa.',
        self::SIBUK         => 'Layanan AI sedang sibuk atau bermasalah. Coba lagi beberapa saat lagi.',
        self::KONEKSI       => 'Tidak dapat terhubung ke penyedia AI (jaringan/timeout). Coba lagi.',
        self::DITOLAK_MODEL => 'Permintaan ditolak oleh model AI. Ubah isi informasi lalu coba lagi.',
        self::TERPOTONG     => 'Hasil AI terpotong sebelum selesai. Coba lagi dengan informasi yang lebih ringkas.',
        self::FORMAT        => 'Hasil AI tidak dalam format yang diharapkan. Silakan generate ulang.',
        self::PERMINTAAN    => 'Permintaan ke penyedia AI tidak valid. Periksa model yang dipilih di pengaturan.',
        self::TIDAK_DIKENAL => 'Terjadi kesalahan saat menghubungi penyedia AI.',
    ];

    public function __construct(public readonly string $kode, ?int $httpStatus = null, ?Throwable $previous = null)
    {
        parent::__construct(self::PESAN[$kode] ?? self::PESAN[self::TIDAK_DIKENAL], $httpStatus ?? 0);
        // Sengaja TIDAK meneruskan $previous: exception asli dari SDK/HTTP
        // client dapat memuat header/permintaan (termasuk token) dan bisa
        // ikut tercetak oleh error handler/log.
        unset($previous);
    }

    /**
     * Petakan kode status HTTP penyedia ke kategori kesalahan yang aman.
     */
    public static function dariStatus(?int $status): self
    {
        $kode = match (true) {
            $status === 401                    => self::TOKEN_INVALID,
            $status === 403                    => self::AKSES_DITOLAK,
            $status === 404                    => self::MODEL_INVALID,
            $status === 400, $status === 422   => self::PERMINTAAN,
            $status === 402, $status === 429   => self::KUOTA,
            $status === 408                    => self::KONEKSI,
            $status !== null && $status >= 500 => self::SIBUK,
            default                            => self::TIDAK_DIKENAL,
        };

        return new self($kode, $status);
    }
}
