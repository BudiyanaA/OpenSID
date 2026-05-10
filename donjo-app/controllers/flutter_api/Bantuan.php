<?php

use App\Models\Bantuan as BantuanModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Bantuan extends MY_Controller
{

  public function __construct()
    {
        parent::__construct();
        $this->load->model(['program_bantuan_model']);
    }
    public function index()
    {
        $bantuan = BantuanModel::get();

        return json([
          'status' => 200,
          'data' => $bantuan
        ]);
    }

    public function insert()
{
    $this->form_validation->set_rules('cid', 'Sasaran', 'required');
    $this->form_validation->set_rules('nama', 'Nama Program', 'required');
    $this->form_validation->set_rules('sdate', 'Tanggal awal', 'required');
    $this->form_validation->set_rules('edate', 'Tanggal akhir', 'required');
    $this->form_validation->set_rules('asaldana', 'Asal Dana', 'required');

    if ($this->form_validation->run() === false) {
        return json([
            'status' => 400,
            'errors' => $this->form_validation->error_array()
        ]);
    }
    $data = [
        'sasaran'  => $this->input->post('cid'),
        'nama'     => nomor_surat_keputusan($this->input->post('nama')),
        'ndesc'    => htmlentities($this->input->post('ndesc')),
        'asaldana' => $this->input->post('asaldana'),
        'sdate'    => date('Y-m-d', strtotime($this->input->post('sdate'))),
        'edate'    => date('Y-m-d', strtotime($this->input->post('edate'))),
        'kk_level' => ($this->input->post('cid') == 2) ? json_encode($this->input->post('kk_level')) : null,
        'config_id' => $this->config_id
    ];
    $data = BantuanModel::create($data);

    return json([
        'status' => 200,
        'data' => $data
    ]);
}
}