<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Http\Controllers;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Smart Desa AI adalah fitur internal SIMDESA (BYOK), BUKAN modul
 * premium / Paket Tambahan.
 *
 * AdminModulController memanggil activate() yang mengalihkan modul di
 * folder Modules/ yang tidak termasuk MODUL_BAWAAN ke halaman Paket
 * Tambahan bila tidak ada langganan aktif. Trait ini menonaktifkan
 * pengecekan tersebut KHUSUS untuk controller Smart Desa AI; mekanisme
 * paket untuk modul lain tidak berubah.
 */
trait FiturInternal
{
    protected function activate()
    {
        // Tanpa pengecekan paket/langganan.
    }

    protected function isModulePremiumActive(): bool
    {
        return true;
    }
}
