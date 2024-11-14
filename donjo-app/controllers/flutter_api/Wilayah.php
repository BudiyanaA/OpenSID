<?php

use App\Models\Wilayah as WilayahModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Wilayah extends MY_Controller
{
    public function index()
    {
        $model = WilayahModel::dusun()->with(['kepala'])->orderBy('urut')->withCount('rts', 'rws', 'keluargaAktif', 'pendudukPria', 'pendudukWanita');
        // die(var_dump($model));
        $data = $model->get();

        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}
