<?php

use App\Models\Kelompok as KelompokModel;
use App\Models\KelompokAnggota as KelompokAnggota;

defined('BASEPATH') || exit('No direct script access allowed');

class Lembaga extends MY_Controller
{
    public function index()
    {
        $kelompok = KelompokModel::with(['ketua', 'kelompokMaster', 'kelompokAnggota'])
        ->where('tipe', 'lembaga');;
        $data = $kelompok->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }

    public function insert()
{
    $data = $this->validate($this->input->post());
    $getKelompok = KelompokModel::tipe($this->tipe)->where('kode', $data['kode'])->exists();

    if ($getKelompok) {
        return response()->json([
            'status'  => 400,
            'message' => "Kode ini {$data['kode']} tidak bisa digunakan. Silahkan gunakan kode yang lain!"
        ], 400);
    }

    $kelompok = new KelompokModel($data);
    $kelompok->save();

    $kelompokAnggota = new KelompokAnggota([
        'id_kelompok' => $kelompok->id,
        'config_id'   => identitas('id'),
        'id_penduduk' => $data['id_ketua'],
        'no_anggota'  => 1,
        'jabatan'     => 1,
        'keterangan'  => "Ketua {$this->tipe}",
        'tipe'        => $this->tipe,
    ]);
    $kelompokAnggota->save();

    return response()->json([
        'status'  => 200,
        'data'    => [
            'kelompok'        => $kelompok,
            'kelompokAnggota' => $kelompokAnggota
        ]
    ], 200);
}

protected function validate($request = [], $id = null)
{
    $data = [];

    if (isset($request['id_ketua'])) {
        $data['id_ketua'] = bilangan($request['id_ketua']);
    }

    $data['id_master']  = bilangan($request['id_master']);
    $data['nama']       = nama_terbatas($request['nama']);
    $data['keterangan'] = htmlentities($request['keterangan']);
    $data['kode']       = nomor_surat_keputusan($request['kode']);
    $data['tipe']       = $this->tipe;

    if (null === $id) {
        $data['slug']      = unique_slug('kelompok', $data['nama']);
        $data['config_id'] = identitas('id');
    }

    return $data;
}
}