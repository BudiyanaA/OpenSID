<?php

use App\Models\KeuanganManualRinci as KeuanganManualRinciModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Keuangan extends MY_Controller
{
    public function index()
    {
        $keuangan = KeuanganManualRinciModel::get();

        return json([
          'status' => 200,
          'data' => $keuangan
        ]);
    }
}