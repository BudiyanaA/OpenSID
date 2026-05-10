<?php

use App\Models\Suplemen as SuplementModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Suplement extends MY_Controller
{
    public function index()
    {
        $suplement = SuplementModel::with(['terdata']);
        $data = $suplement->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }

    public function insert()
    {
      $data = SuplementModel::create(static::validated($this->request));
      return json([
        'status' => 200,
        'data' => $data
      ]);
    }

    protected static function validated($request = [])
    {
        return [
            'sasaran'    => $request['sasaran'],
            'nama'       => nomor_surat_keputusan($request['nama']),
            'keterangan' => strip_tags($request['keterangan']),
        ];
    }
}