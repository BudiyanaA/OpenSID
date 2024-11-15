<?php

use App\Models\PesanMandiri as PesanMandiriModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Mailbox extends MY_Controller
{
    public function index()
    {
        $pesan = PesanMandiriModel::get();

        return json([
          'status' => 200,
          'data' => $pesan
        ]);
    }
}