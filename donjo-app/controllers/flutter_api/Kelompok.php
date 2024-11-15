<?php

use App\Models\Kelompok as KelompokModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Kelompok extends MY_Controller
{
    public function index()
    {
        $kelompok = KelompokModel::with(['ketua', 'kelompokMaster', 'kelompokAnggota']);
        $data = $kelompok->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}