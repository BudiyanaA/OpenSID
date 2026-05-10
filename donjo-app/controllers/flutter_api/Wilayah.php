<?php

use App\Models\Wilayah as WilayahModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Wilayah extends MY_Controller
{
    public function index()
    {
        $model = WilayahModel::dusun()->with(['kepala'])->orderBy('urut')->withCount('rts', 'rws', 'keluargaAktif', 'pendudukPria', 'pendudukWanita');
        $data = $model->get();

        return json([
          'status' => 200,
          'data' => $data
        ]);
    }

    public function insert()
    {
        $data = $this->bersihkan_data($this->request);

        WilayahModel::create($data);
        // insert rw
        $data['rw'] = '-';
        WilayahModel::create($data);
        // insert rt
        $data['rt'] = '-';
        WilayahModel::create($data);

        return json([
          'status' => 200,
          'data' => $data
        ]);
    }

    private function bersihkan_data($data)
    {
        if ((int) $data['id_kepala'] === 0) {
            unset($data['id_kepala']);
        }

        $data['dusun'] = nama_terbatas(trim(str_ireplace('DUSUN', '', $data['dusun'])));
        $data['rw']    = bilangan($data['rw']) ?: 0;
        $data['rt']    = bilangan($data['rt']) ?: 0;

        return $data;
    }
}
