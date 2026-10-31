<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

return [
    'name' => 'SmartDesaAi',

    /*
     * Jenis artikel (kode => label). Dipakai sebagai konteks prompt, bukan
     * kategori artikel; kategori tetap dipilih dari kategori yang sudah ada.
     */
    'jenis_artikel' => [
        'berita_desa'          => 'Berita Desa',
        'pemerintahan_desa'    => 'Pemerintahan Desa',
        'pembangunan_desa'     => 'Pembangunan Desa',
        'kegiatan_masyarakat'  => 'Kegiatan Masyarakat',
        'kegiatan_desa'        => 'Kegiatan Desa',
        'pengumuman'           => 'Pengumuman',
        'transparansi_desa'    => 'Transparansi Desa',
        'potensi_desa'         => 'Potensi Desa',
        'prestasi_desa'        => 'Prestasi Desa',
        'pelayanan_masyarakat' => 'Pelayanan Masyarakat',
    ],

    /*
     * Gaya penulisan (kode => [label, panduan untuk model]).
     */
    'gaya_artikel' => [
        'formal'    => ['Formal Pemerintahan', 'Bahasa Indonesia baku dan formal khas rilis resmi pemerintah desa; kalimat lugas, sopan, tanpa bahasa gaul.'],
        'berita'    => ['Berita Desa', 'Gaya jurnalistik berita: paragraf pembuka menjawab apa, siapa, kapan, di mana (bila datanya tersedia); piramida terbalik.'],
        'informatif' => ['Informatif', 'Menjelaskan secara runtut dan mudah dipahami warga; boleh memakai subjudul dan daftar poin.'],
        'seo'       => ['SEO Friendly', 'Judul jelas memuat kata kunci utama dan nama desa; subjudul deskriptif; paragraf pendek; kata kunci muncul alami tanpa diulang berlebihan.'],
    ],

    // Batas panjang isian formulir (karakter)
    'batas_input' => [
        'topik'      => 200,
        'informasi'  => 6000,
        'tanggal'    => 100,
        'lokasi'     => 200,
        'narasumber' => 300,
    ],

    // Batas token keluaran dan timeout HTTP ke penyedia (detik)
    'max_tokens' => 16000,
    'timeout'    => 120,
];
