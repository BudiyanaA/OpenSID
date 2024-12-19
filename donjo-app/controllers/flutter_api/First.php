<?php

// use App\Models\Wilayah as WilayahModel;

defined('BASEPATH') || exit('No direct script access allowed');

class First extends MY_Controller
{
    public function index()
    {
        $this->load->model('theme_model');
        $this->load->model('first_menu_m');
        $this->load->model('first_artikel_m');
        $this->load->model('web_widget_model');
        $this->load->model('laporan_penduduk_model');

        $desa = identitas();
        $theme        = $this->theme_model->tema;
        $theme_folder = $this->theme_model->folder;
        
        $file = dirname(__FILE__, 4) . '/desa/themes/denatra/partials/video/video.json';
        $json = file_get_contents($file);
        $array = json_decode($json, true);

        $data = [
          'logo_desa' => gambar_desa($desa['logo']),
          'header_bg' => base_url("$theme_folder/$theme/assets/img/header.jpg"),
          'desa' => $desa,
          'setting' => $this->setting,
          'menu_kiri' => $this->first_menu_m->list_menu_kiri(),
          'menu_tema' => menu_tema(),
          // 'random_doa' => getRandomDoa(),
          'headline' => $this->first_artikel_m->get_headline(),
          'video' => $array['video'],
          'w_cos' => $this->web_widget_model->get_widget_aktif(),
          'stat_widget' => $this->laporan_penduduk_model->list_data(4),
          'komen' => $this->first_artikel_m->komentar_show(),
          'artikel' => $this->first_artikel_m->artikel_show($data['paging']->offset, $data['paging']->per_page),
        ];
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }
}
