<?php

use App\Models\Keluarga as KeluargaModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Keluarga extends MY_Controller
{
    public function index()
    {
        $keluarga = KeluargaModel::with(['kepalaKeluarga', 'anggota', 'Wilayah'])->get();

        return json([
          'status' => 200,
          'data' => $keluarga
        ]);
    }

    public function insert()
  {
      $data = $this->input->post();
      $valid = KeluargaModel::validasi_data_keluarga($data);

      if (! $valid['status']) {
          return json([
              'status' => 400,
              'error' => true,
              'messages' => $valid['messages']
          ]);
      }

      try {
          KeluargaModel::tambahKeluargaDariPenduduk($data);
          return json([
              'status' => 200,
              'error' => false,
              'data' => $data
          ]);
      } catch (Exception $e) {
          log_message('error', $e->getMessage());
          return json([
              'status' => 500,
              'error' => true,
              'messages' => 'Keluarga baru gagal ditambahkan. Silakan coba lagi.'
          ]);
      }
  }
}