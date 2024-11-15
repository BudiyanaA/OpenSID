<?php

use App\Models\PendudukMandiri as PendudukMandiriModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Mandiri extends MY_Controller
{
    public function index()
    {
        $mandiri = PendudukMandiriModel::get();

        return json([
          'status' => 200,
          'data' => $mandiri
        ]);
    }
}