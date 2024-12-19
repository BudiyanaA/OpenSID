<?php

use App\Libraries\Release;
use App\Models\Shortcut;

defined('BASEPATH') || exit('No direct script access allowed');

class Beranda extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->isAdmin = $this->session->isAdmin->pamong;
        $this->load->model('pelanggan_model');
    }
    public function index(){
        get_pesan_opendk(); //ambil pesan baru di opendk

        $this->load->library('saas');
        $data = [
            'rilis'           => $this->getUpdate(),
            'shortcut'        => Shortcut::querys()['data'],
            'saas'            => $this->saas->peringatan(),
            'notif_langganan' => $this->pelanggan_model->status_langganan(),
        ];
        return json([
            'status' => 200,
            'data' => $data
          ]);
    }

    private function getUpdate(): array
    {
        $info = [];

        if (cek_koneksi_internet() && ! config_item('demo_mode')) {
            $url_rilis = config_item('rilis_umum');

            $release = new Release();
            $release->setApiUrl($url_rilis)->setCurrentVersion();

            if ($release->isAvailable()) {
                $info['update_available'] = $release->isAvailable();
                $info['current_version']  = 'v' . AmbilVersi();
                $info['latest_version']   = $release->getLatestVersion();
                $info['release_name']     = $release->getReleaseName();
                $info['release_body']     = $release->getReleaseBody();
                $info['url_download']     = $release->getReleaseDownload();
            } else {
                $info['update_available'] = false;
            }
        }

        return $info;
    }
}
