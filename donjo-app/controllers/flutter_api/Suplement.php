<?php

use App\Models\Suplemen as SuplementModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Suplement extends MY_Controller
{
    public function index()
    {
        $suplement = SuplementModel::with('terdata');
        $data = $suplement->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}