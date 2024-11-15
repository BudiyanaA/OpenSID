<?php

use App\Models\Rtm as RtmModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Rtm extends MY_Controller
{
    public function index()
    {
        $rtm = RtmModel::with(['kepalaKeluarga', 'anggota'])->get();

        return json([
          'status' => 200,
          'data' => $rtm
        ]);
    }
}