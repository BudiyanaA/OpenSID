<?php

use App\Models\Penduduk as PendudukModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Penduduk extends MY_Controller
{
    public function index()
    {
        $penduduk = PendudukModel::get();

        return json([
          'status' => 200,
          'data' => $penduduk
        ]);
    }

    public function insert($peristiwa)
    {
      $data                    = $this->input->post();
      $originalInput           = $data;
      $data['tgl_lapor']       = rev_tgl($data['tgl_lapor']);
      $data['tgl_peristiwa']   = $data['tgl_peristiwa'] ? rev_tgl($data['tgl_peristiwa']) : rev_tgl($data['tanggallahir']);
      $data['jenis_peristiwa'] = $peristiwa;
      $validasiPenduduk        = PendudukModel::validasi($data);
      unset($data['file_foto'], $data['old_foto'], $data['nik_lama'], $data['dusun'], $data['rw']);

      DB::beginTransaction();
      try {
        $penduduk = PendudukModel::baru($data);
        DB::commit();
      return json([
        'status' => 200,
        'data' => $data
      ]);
    } catch (Exception $e) {
      log_message('error', $e->getMessage());
      DB::rollBack();
      set_session('old_input', $originalInput);
      return json([
        'status' => 500,
        'message' => 'Rumah Tangga gagal disimpan',
        'error' => $e->getMessage() 
    ], 500);
  }
    }
}