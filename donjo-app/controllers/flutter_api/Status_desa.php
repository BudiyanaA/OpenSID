<?php


defined('BASEPATH') || exit('No direct script access allowed');

class Status_desa extends MY_Controller
{
    public function index()
    {

        $type = $this->input->get('type') ?? 'sdgs';

        if ($type === 'sdgs') {
            return $this->sdgs();
        }

        return $this->idm();
    }

    private function sdgs()
    {
        set_session('navigasi', 'sdgs');

        $data = [
            'status' => 'success',
            'type' => 'sdgs',
            'data' => [
                'sdgs' => sdgs(),
                'kode_desa' => identitas('kode_desa'),
            ]
        ];

        return json([
            'status' => 200,
            'data'   => $data
        ]);
    }

    private function idm()
    {
        $tahun = session('tahun') ?? ($this->input->post('tahun') ?? (setting('tahun_idm')) ?? date('Y'));

        $data = [
            'status' => 'success',
            'type' => 'idm',
            'data' => [
                'tahun' => (int) $tahun,
                'idm'   => idm(identitas('kode_desa'), $tahun),
            ]
        ];

        return json([
            'status' => 200,
            'data'   => $data
        ]);
    }
}