<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Models;

use App\Models\BaseModel;
use App\Traits\ConfigId;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Catatan pemakaian Smart Desa AI (untuk kontrol biaya & batas pemakaian).
 *
 * Tidak menyimpan token, prompt, maupun isi artikel.
 */
class SmartDesaAiLog extends BaseModel
{
    use ConfigId;

    public const AKSI_GENERATE = 'generate';
    public const AKSI_UJI      = 'uji_koneksi';
    public const AKSI_DRAFT    = 'simpan_draft';
    public const AKSI_PUBLISH  = 'publish';

    public const SUKSES = 'sukses';
    public const GAGAL  = 'gagal';

    protected $table = 'smart_desa_ai_log';

    protected $fillable = [
        'user_id',
        'aksi',
        'provider',
        'model',
        'jenis_artikel',
        'status',
        'kode_error',
        'input_tokens',
        'output_tokens',
        'durasi_ms',
        'artikel_id',
    ];

    protected $casts = [
        'input_tokens'  => 'integer',
        'output_tokens' => 'integer',
        'durasi_ms'     => 'integer',
    ];

    public function scopeGenerate($query)
    {
        return $query->where('aksi', self::AKSI_GENERATE);
    }
}
