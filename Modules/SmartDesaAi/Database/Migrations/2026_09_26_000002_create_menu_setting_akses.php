<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

use App\Models\GrupAkses;
use App\Models\Modul;
use App\Traits\Migrator;
use Illuminate\Database\Eloquent\Model;

/**
 * Menu, pengaturan, dan hak akses Smart Desa AI.
 *
 * Idempoten: createModul/createSetting/createHakAkses memakai upsert, dan
 * nilai setting yang sudah ada (termasuk token) TIDAK ditimpa saat
 * migrasi dijalankan ulang. Tidak mengubah/memindahkan menu yang ada;
 * hanya MENAMBAH dua sub-menu.
 */
return new class () {
    use Migrator;

    public function up(): void
    {
        Model::unguard();
        $configId = identitas('id');

        // Admin Web → Smart Desa AI (generator artikel)
        $this->createModul([
            'config_id'   => $configId,
            'modul'       => 'Smart Desa AI',
            'slug'        => 'smart-desa-ai',
            'url'         => 'smart_desa_ai',
            'ikon'        => 'fa-magic',
            'level'       => 4, // setara menu Artikel
            'parent_slug' => 'admin-web',
        ]);

        // Pengaturan → Smart Desa AI (khusus Administrator secara bawaan)
        $this->createModul([
            'config_id'   => $configId,
            'modul'       => 'Smart Desa AI',
            'slug'        => 'smart-desa-ai-pengaturan',
            'url'         => 'smart_desa_ai_pengaturan',
            'ikon'        => 'fa-magic',
            'level'       => 1, // setara menu Pengaturan → Aplikasi
            'parent_slug' => 'pengaturan',
        ]);

        // Generator mengikuti hak akses modul Artikel yang sudah ada: grup yang
        // boleh mengelola artikel boleh membuat artikel dengan Smart Desa AI.
        $this->salinHakAkses($configId, 'artikel', 'smart-desa-ai');

        $this->createSettings([
            [
                'config_id'  => $configId,
                'judul'      => 'Penyedia AI',
                'key'        => 'smart_desa_ai_provider',
                'value'      => 'claude',
                'keterangan' => 'Penyedia AI yang dipakai Smart Desa AI (claude / openai)',
                'jenis'      => 'text',
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Model AI',
                'key'        => 'smart_desa_ai_model',
                'value'      => 'claude-opus-5',
                'keterangan' => 'ID model AI yang dipakai untuk generate artikel',
                'jenis'      => 'text',
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Token API AI',
                'key'        => 'smart_desa_ai_token',
                'value'      => '',
                'keterangan' => 'Token API milik desa (tersimpan terenkripsi, tidak pernah ditampilkan)',
                'jenis'      => 'text',
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Status Koneksi AI',
                'key'        => 'smart_desa_ai_status',
                'value'      => '',
                'keterangan' => 'Hasil uji koneksi terakhir (JSON: status, waktu, jumlah model)',
                'jenis'      => 'text',
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Daftar Model AI',
                'key'        => 'smart_desa_ai_models',
                'value'      => '[]',
                'keterangan' => 'Daftar model dari uji koneksi terakhir (JSON)',
                'jenis'      => 'text',
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Batas Generate per Pengguna per Jam',
                'key'        => 'smart_desa_ai_batas_user_jam',
                'value'      => '10',
                'keterangan' => 'Maksimal generate artikel per pengguna dalam 1 jam',
                'jenis'      => 'input-number',
                'attribute'  => ['class' => 'required', 'min' => 1, 'max' => 1000, 'step' => 1],
                'kategori'   => 'smart_desa_ai',
            ],
            [
                'config_id'  => $configId,
                'judul'      => 'Batas Generate per Hari (Desa)',
                'key'        => 'smart_desa_ai_batas_harian',
                'value'      => '50',
                'keterangan' => 'Maksimal generate artikel per hari untuk seluruh pengguna desa ini',
                'jenis'      => 'input-number',
                'attribute'  => ['class' => 'required', 'min' => 1, 'max' => 10000, 'step' => 1],
                'kategori'   => 'smart_desa_ai',
            ],
        ]);

        cache()->flush();
    }

    public function down(): void
    {
        $configId = identitas('id');
        $this->deleteSetting(['config_id' => $configId, 'kategori' => 'smart_desa_ai']);
        $this->deleteModul(['config_id' => $configId, 'slug' => 'smart-desa-ai']);
        $this->deleteModul(['config_id' => $configId, 'slug' => 'smart-desa-ai-pengaturan']);
        cache()->flush();
    }

    /**
     * Salin hak akses grup dari modul sumber ke modul tujuan. Hanya menambah
     * grup yang belum punya akses, sehingga pengaturan hak akses yang sudah
     * diubah admin tidak ter-reset saat migrasi dijalankan ulang.
     */
    private function salinHakAkses(int $configId, string $slugSumber, string $slugTujuan): void
    {
        $sumber = Modul::withoutConfigId($configId)->where('slug', $slugSumber)->value('id');
        $tujuan = Modul::withoutConfigId($configId)->where('slug', $slugTujuan)->value('id');

        if (! $sumber || ! $tujuan) {
            return;
        }

        GrupAkses::withoutConfigId($configId)
            ->where('id_modul', $sumber)
            ->get(['id_grup', 'akses'])
            ->each(function ($akses) use ($configId, $tujuan): void {
                $sudahAda = GrupAkses::withoutConfigId($configId)
                    ->where('id_grup', $akses->id_grup)
                    ->where('id_modul', $tujuan)
                    ->exists();

                if ($sudahAda) {
                    return;
                }

                $this->createHakAkses([
                    'config_id' => $configId,
                    'id_grup'   => $akses->id_grup,
                    'id_modul'  => $tujuan,
                    'akses'     => $akses->akses,
                ]);
            });
    }
};
