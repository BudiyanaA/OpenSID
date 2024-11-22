<?php

use App\Models\KeuanganManualRinci as KeuanganManualRinciModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Keuangan extends MY_Controller
{

  public function __construct()
  {
      parent::__construct();
      $this->load->model(['keuangan_manual_model', 'keuangan_grafik_manual_model']);
  }

    public function index()
    {
        $keuangan = KeuanganManualRinciModel::get();

        return json([
          'status' => 200,
          'data' => $keuangan
        ]);
    }

    public function simpan_anggaran()
    {
      $insert = $this->validation($this->input->post());
      $data   = $this->keuangan_manual_model->simpan_anggaran($insert);

      return json([
        'status' => 200,
        'data' => $data
      ]);
    }

    private function validation($post = []): array
    {
        return [
            'Tahun'           => bilangan($post['Tahun']),
            'Kd_Akun'         => $this->security->xss_clean($post['Kd_Akun']),
            'Kd_Keg'          => $this->security->xss_clean($post['Kd_Keg']),
            'Kd_Rincian'      => $this->security->xss_clean($post['Kd_Rincian']),
            'Nilai_Anggaran'  => ltrim(bilangan_titik($post['Nilai_Anggaran']), '0') ?: '0.00',
            'Nilai_Realisasi' => ltrim(bilangan_titik($post['Nilai_Realisasi']), '0') ?: '0.00',
        ];
    }
}