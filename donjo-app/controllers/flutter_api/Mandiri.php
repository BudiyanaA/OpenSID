<?php

use App\Models\Config;
use App\Models\PendudukMandiri as PendudukMandiriModel;
use App\Models\PendudukHidup as PendudukHidupModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Mandiri extends MY_Controller
{
    public $config_id;

    public function __construct()
    {
        parent::__construct();

        $this->config_id = Config::appKey()->first()->id;
    }

    public function index()
    {
        $mandiri = PendudukMandiriModel::get();

        return json([
          'status' => 200,
          'data' => $mandiri
        ]);
    }

    public function masuk()
    {
        $masuk = $this->input->post();
        $nik   = bilangan(bilangan($masuk['nik']));
        $pin   = hash_pin(bilangan($masuk['pin']));

        $data = $this->config_id('pm')
          ->select('pm.*, p.nama, p.nik, p.tag_id_card, p.sex, p.foto, p.kk_level, p.id_kk, k.no_kk, c.rt, c.rw, c.dusun')
          ->from('tweb_penduduk_mandiri pm')
          ->join('penduduk_hidup p', 'pm.id_pend = p.id')
          ->join('tweb_keluarga k', 'p.id_kk = k.id', 'left')
          ->join('tweb_wil_clusterdesa c', 'p.id_cluster = c.id', 'left')
          ->where('p.nik', $nik)
          ->get()
          ->row();

        if (!$data || $pin != $data->pin) {
          return json([
            'status' => 401,
            'message' => 'Login Failed',
            'data' => null
          ]);
        }

        $token = base64_encode(json_encode($data));

        return json([
          'status' => 200,
          'message' => 'Login Success',
          'data' => $data,
          'token' => $token,
        ]);
    }

    private function config_id(?string $alias = null, bool $boleh_null = false)
    {
        $this->db->group_start();
        if ($alias) {
            $this->db->where("{$alias}.config_id", $this->config_id);

            if ($boleh_null) {
                $this->db->or_where("{$alias}.config_id", null);
            }
        } else {
            $this->db->where('config_id', $this->config_id);

            if ($boleh_null) {
                $this->db->or_where('config_id', null);
            }
        }
        $this->db->group_end();

        return $this->db;
    }

    public function insert()
    {
      $mandiri = new PendudukMandiriModel();
            $pin     = bilangan($this->request['pin'] ?: $mandiri->generate_pin());

            $mandiri->pin     = hash_pin($pin); // Hash PIN
            $mandiri->id_pend = $this->request['id_pend'];
            $mandiri->save();

            // Ambil data sementara untuk ditampilkan
            $flash        = PendudukHidupModel::find($this->request['id_pend'])->toArray();
            $flash['pin'] = $pin;
            set_session('info', $flash);
            return json([
              'status' => 200,
              'data' => $mandiri
            ]);
    }
}