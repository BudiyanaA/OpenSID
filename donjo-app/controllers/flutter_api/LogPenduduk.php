<?php

use App\Models\LogPenduduk as LogPendudukModel;

defined('BASEPATH') || exit('No direct script access allowed');

class LogPenduduk extends MY_Controller
{
    public function index()
    {
      $penduduk = LogPendudukModel::with('penduduk')->get();

        return json([
          'status' => 200,
          'data' => $penduduk
        ]);
    }
}