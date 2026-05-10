<?php

use App\Models\PermohonanSurat as PermohonanSuratModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Permohonan_surat_admin extends MY_Controller
{
    public function index()
    {
        $permohonanSurat = PermohonanSuratModel::get();

        return json([
          'status' => 200,
          'data' => $permohonanSurat
        ]);
    }
}