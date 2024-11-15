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
}