<?php

use App\Models\Bantuan as BantuanModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Bantuan extends MY_Controller
{
    public function index()
    {
        $bantuan = BantuanModel::get();

        return json([
          'status' => 200,
          'data' => $bantuan
        ]);
    }
}