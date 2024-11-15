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
}