<?php

use App\Models\Penduduk;
use App\Models\Wilayah;

defined('BASEPATH') || exit('No direct script access allowed');

class Dpt extends MY_Controller
{
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $tglPemilihan = $this->input->get('tgl_pemilihan') ?? date('d-m-Y');
            $sex          = $this->input->get('sex');
            $dusun        = $this->input->get('dusun');
            $rw           = $this->input->get('rw');
            $rt           = $this->input->get('rt');
            $advanced     = $this->input->get('advanced');

            // Proses data sumber
            $data = $this->sumberData($tglPemilihan, $sex, $dusun, $rw, $rt, $advanced)->get()->toArray();

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 200,
                    'data'   => $data
                ]));
        }

        return show_404();
    }

    private function sumberData($tglPemilihan, $sex, $dusun, $rw, $rt, $advanced)
    {
        $umurFilter     = $advanced['umur'] ?? null;
        $filterKategori = [];
        $tagIdFilter    = null;

        if (!empty($advanced['search'])) {
            parse_str($advanced['search'], $kategoriFilter);
            foreach ($kategoriFilter as $key => $val) {
                if (trim($val) !== '') {
                    $filterKategori[$key] = $val;
                }
            }
        }

        if (isset($filterKategori['tag_id_card'])) {
            $tagIdFilter = $filterKategori['tag_id_card'];
            unset($filterKategori['tag_id_card']);
        }

        $listCluster = [];
        if ($dusun) {
            $cluster = new Wilayah();
            $cluster = $cluster->whereDusun($dusun);
            if ($rw) {
                $cluster = $cluster->whereRw($rw);
                if ($rt) {
                    $cluster = $cluster->whereRt($rt);
                }
            }
            $listCluster = $cluster->select(['id'])->get()->pluck('id', 'id')->toArray();
        }

        return Penduduk::batasiUmur($tglPemilihan, $umurFilter)
            ->dpt($tglPemilihan)
            ->when($tagIdFilter, static fn ($q) => $tagIdFilter == '1' ? $q->whereNotNull('tag_id_card') : $q->whereNull('tag_id_card'))
            ->when($filterKategori, static fn ($q) => $q->where($filterKategori))
            ->when($sex, static fn ($q) => $q->where('sex', $sex))
            ->when($listCluster, static fn ($q) => $q->whereIn('id_cluster', $listCluster))
            ->withOnly(['jenisKelamin', 'keluarga', 'wilayah', 'pendidikanKK', 'pekerjaan', 'statusKawin']);
    }
}