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
      if (! $validasiPenduduk['status']) {
          set_session('old_input', $originalInput);
          redirect_with('error', $validasiPenduduk['messages'], ci_route('penduduk.form_peristiwa', $data['jenis_peristiwa']));
      }
      unset($data['file_foto'], $data['old_foto'], $data['nik_lama'], $data['dusun'], $data['rw']);

      DB::beginTransaction();

      return json([
        'status' => 200,
        'data' => $data
      ]);
    }
}