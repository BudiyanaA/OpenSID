<?php

use App\Models\Penduduk as PendudukModel;

defined('BASEPATH') || exit('No direct script access allowed');

class PendaftaranKerjasama extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        isCan('b');

        // jangan aktifkan jika demo dan di domain whitelist
        if (config_item('demo_mode') && in_array(get_domain(APP_URL), WEBSITE_DEMO)) {
            show_404();
        }

        $this->load->model(['surat_model', 'pamong_model']);
        $this->client = new Client();
        $this->server = config_item('server_layanan');
    }

    public function insert()
{
    $this->load->library('upload');
    
    // Konfigurasi upload
    $config['upload_path']   = LOKASI_DOKUMEN;
    $config['file_name']     = 'dokumen-permohonan.pdf';
    $config['allowed_types'] = 'pdf';
    $config['max_size']      = 1024;
    $config['overwrite']     = true;
    
    $this->upload->initialize($config);

    // Proses upload
    if (!$this->upload->do_upload('permohonan')) {
        // Jika gagal upload
        return json_encode([
            'status' => 400,
            'error' => $this->upload->display_errors(),
        ]);
    }

    // Jika berhasil upload, siapkan data untuk multipart
    $file_path = LOKASI_DOKUMEN . 'dokumen-permohonan.pdf';

    $multipart_data = [
        ['name' => 'user_id', 'contents' => (int) $this->input->post('user_id')],
        ['name' => 'email', 'contents' => email($this->input->post('email'))],
        ['name' => 'desa', 'contents' => bilangan_titik($this->input->post('desa'))],
        ['name' => 'domain', 'contents' => alamat_web($this->input->post('domain'))],
        ['name' => 'kontak_no_hp', 'contents' => bilangan($this->input->post('kontak_no_hp'))],
        ['name' => 'kontak_nama', 'contents' => nama($this->input->post('kontak_nama'))],
        ['name' => 'status_langganan', 'contents' => (int) $this->input->post('status_langganan_id')],
        ['name' => 'permohonan', 'contents' => Psr7\Utils::tryFopen($file_path, 'r')],
    ];

    // Kirim respon JSON
    return json_encode([
        'status' => 200,
        'data' => $multipart_data,
    ]);
}
}