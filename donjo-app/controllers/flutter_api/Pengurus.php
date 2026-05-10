<?php

use App\Enums\JenisKelaminEnum;
use App\Enums\AgamaEnum;
use App\Enums\PendidikanKKEnum;
use App\Models\Pamong;


defined('BASEPATH') || exit('No direct script access allowed');

class Pengurus extends MY_Controller
{
    public function index() {
            $status = $this->input->get('status') ?? null;
    
            $data = Pamong::urut()
                ->when($status, function ($q) use ($status) {
                    return $q->where('pamong_status', $status);
                })
                ->get()
                ->map(function ($item) {
                    return [
                    'foto' => AmbilFoto($item->foto, '', $item->pamong_sex),
                    'identitas' => $item->pamong_nama,
                    'nip' => $item->pamong_nip,
                    'nik' => $item->pamong_nik,
                    'tag_id_card' =>$item->pamong_tag_id_card,
                    'ttl' => $item->pamong_tempatlahir . ', ' . tgl_indo($item->pamong_tanggallahir),
                    'sex' => JenisKelaminEnum::valueOf($item->pamong_sex ?? $item->penduduk->sex),
                    'agama' => AgamaEnum::valueOf($item->pamong_agama),
                    'pendidikan_kk' => PendidikanKKEnum::valueOf($item->pamong_pendidikan ?? $item->penduduk->pendidikan_kk_id),
                    'jabatan' => $item->jabatan,
                    'pamong_tglsk' => $item->pamong_tglsk,
                    'pamong_tglhenti' => $item->pamong_tglhenti,
                    ];
                });
    
                return json([
                    'status' => 200,
                    'data'   => $data
                ]);
    }
}