<?php

// use App\Models\Wilayah as WilayahModel;

defined('BASEPATH') || exit('No direct script access allowed');

use App\Models\PesanMandiri;

class Pesan extends MY_Controller
{
    public function kirim()
    {
        $data = $this->input->post();

        try {
            // TODO: validasi batas pesan

            // $post['penduduk_id'] = $this->is_login->id_pend; // kolom email diisi nik untuk pesan
            // $post['owner']       = $this->is_login->nama;
            // TODO: from token
            $post['penduduk_id'] = 4; // kolom email diisi nik untuk pesan
            $post['owner']       = 'jumardi';

            $post['subjek']      = $data['subjek'];
            $post['komentar']    = $data['pesan'];
            $post['tipe']        = PesanMandiri::MASUK;
            $post['status']      = PesanMandiri::UNREAD;
            PesanMandiri::create($post);

            // TODO: notifikasi telegram

            return json([
                'status' => 200,
                'message' => 'Pesan berhasil dikirim',
                'data' => $post
            ], 200);

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return json([
                'status' => 500,
                'message' => 'Pesan gagal dikirim',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}