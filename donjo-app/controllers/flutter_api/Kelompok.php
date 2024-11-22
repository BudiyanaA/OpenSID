<?php

use App\Models\Kelompok as KelompokModel;
use App\Models\KelompokAnggota as KelompokAnggota;


defined('BASEPATH') || exit('No direct script access allowed');

class Kelompok extends MY_Controller
{
    protected $tipe              = 'kelompok';
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['kelompok_model', 'pamong_model']);
        $this->kelompok_model->set_tipe($this->tipe);
    }
    public function index()
    {
        $kelompok = KelompokModel::with(['ketua', 'kelompokMaster', 'kelompokAnggota'])
        ->where('tipe', 'kelompok');;
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
        return json([
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

    return json([
        'status' => 200,
        'data' => $data
      ]);
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