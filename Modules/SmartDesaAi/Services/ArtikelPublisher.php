<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Services;

use App\Enums\StatusEnum;
use App\Enums\TampilanArtikelEnum;
use App\Models\Artikel;
use App\Models\Kategori;
use InvalidArgumentException;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Menyimpan hasil Smart Desa AI ke sistem ARTIKEL yang sudah ada
 * (tabel/model `artikel`), mengikuti aturan Web::insert / Web::update:
 * - judul dibersihkan dengan judul(), slug unik dengan unique_slug(),
 * - tipe 'dinamis', id_kategori null bila tanpa kategori (-1),
 * - id_user = pengguna yang login,
 * - Kontributor (grup 4) tidak dapat mengaktifkan artikel,
 * - pengguna hanya boleh mengubah artikel yang boleh diubahnya (bolehUbah()).
 *
 * Draft   = enabled 0 (tidak tampil di Website Desa)
 * Publish = enabled 1 (tampil di Website Desa instalasi ini)
 */
class ArtikelPublisher
{
    public const GRUP_KONTRIBUTOR = 4;

    public static function bolehPublish(): bool
    {
        return ci_auth()->id_grup != self::GRUP_KONTRIBUTOR && can('u', 'artikel');
    }

    /**
     * Kategori artikel yang sudah ada (id => nama).
     *
     * @return array<int, string>
     */
    public static function daftarKategori(): array
    {
        return Kategori::query()
            ->where('enabled', StatusEnum::YA)
            ->orderBy('urut')
            ->pluck('kategori', 'id')
            ->all();
    }

    /**
     * Simpan (buat atau perbarui) artikel.
     *
     * @throws InvalidArgumentException pesan aman untuk pengguna
     */
    public function simpan(?int $id, string $judul, string $isiHtml, ?int $idKategori, bool $publish): Artikel
    {
        $judul = judul($judul);
        $isi   = KontenHtml::bersihkan($isiHtml);

        if ('' === trim((string) $judul) || '' === trim(strip_tags($isi))) {
            throw new InvalidArgumentException('Judul atau isi harus diisi.');
        }

        if ($idKategori !== null && $idKategori > 0 && ! Kategori::whereKey($idKategori)->exists()) {
            throw new InvalidArgumentException('Kategori tidak ditemukan.');
        }

        $idKategori = ($idKategori === null || $idKategori <= 0) ? null : $idKategori;

        if ($publish && ! self::bolehPublish()) {
            throw new InvalidArgumentException('Akun Anda tidak dapat mempublikasikan artikel. Simpan sebagai draft agar diperiksa redaksi/admin.');
        }

        $enabled = $publish ? StatusEnum::YA : StatusEnum::TIDAK;

        if ($id) {
            $artikel = Artikel::withOnly([])->find($id);

            if (! $artikel || $artikel->tipe !== 'dinamis') {
                throw new InvalidArgumentException('Artikel tidak ditemukan.');
            }

            if (! $artikel->bolehUbah()) {
                throw new InvalidArgumentException('Pengguna tidak diijinkan mengubah artikel ini.');
            }

            $data = [
                'judul'       => $judul,
                'isi'         => $isi,
                'id_kategori' => $idKategori,
                'enabled'     => $enabled,
            ];

            if ($artikel->judul !== $judul) {
                $data['slug'] = unique_slug('artikel', $judul, $artikel->id);
            }

            // Saat pertama kali dipublikasikan, tanggal tayang = sekarang.
            if ($publish && (int) $artikel->enabled !== StatusEnum::YA) {
                $data['tgl_upload'] = date('Y-m-d H:i:s');
            }

            $artikel->update($data);

            return $artikel->refresh();
        }

        return Artikel::create([
            'judul'          => $judul,
            'isi'            => $isi,
            'id_kategori'    => $idKategori,
            'tipe'           => 'dinamis',
            'id_user'        => ci_auth()->id,
            'enabled'        => $enabled,
            'tgl_upload'     => date('Y-m-d H:i:s'),
            'slug'           => unique_slug('artikel', $judul),
            'boleh_komentar' => StatusEnum::YA,
            'tampilan'       => TampilanArtikelEnum::CONTENT_SIDEBAR_RIGHT,
        ]);
    }

    /**
     * URL editor artikel yang sudah ada (Admin Web → Artikel).
     */
    public static function urlEditor(Artikel $artikel): string
    {
        return site_url('web/form/' . ($artikel->id_kategori ?: -1) . '/' . encrypt($artikel->id));
    }

    /**
     * URL artikel di Website Desa.
     */
    public static function urlWebsite(Artikel $artikel): string
    {
        // Accessor url_slug sudah menghasilkan URL lengkap (artikel/Y/m/d/slug).
        return $artikel->url_slug;
    }
}
