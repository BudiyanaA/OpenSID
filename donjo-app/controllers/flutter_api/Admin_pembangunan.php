<?php

use App\Models\Pembangunan as PembangunanModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Admin_pembangunan extends MY_Controller
{
    public function index()
    {
        $pembangunan = PembangunanModel::with(['pembangunanDokumentasi','wilayah']);
        $data= $pembangunan->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}