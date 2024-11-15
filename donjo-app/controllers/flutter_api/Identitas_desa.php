<?php

use App\Models\Config;
use App\Models\Pamong;

defined('BASEPATH') || exit('No direct script access allowed');

class Identitas_Desa extends MY_Controller
{
    private $cek_kades;
    private $identitas_desa;

    public function __construct()
    {
        parent::__construct();
        $this->cek_kades = Pamong::kepalaDesa()->exists();
        $config = Config::appKey()->first();
        $this->identitas_desa = $config ? $config->toArray() : null;
    }

    public function index()
    {
        $data = [
            'identitas_desa' => $this->identitas_desa,
            'cek_kades'      => $this->cek_kades,
        ];

        return json([
            'status' => 200,
            'data'   => $data
        ]);
    }
}