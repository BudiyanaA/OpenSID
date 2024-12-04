<?php

use App\Models\Artikel;
use App\Models\Agenda;
use App\Enums\StatusEnum;

defined('BASEPATH') || exit('No direct script access allowed');

class Web extends MY_Controller
{
    public function insert($cat)
    {
        $data = $this->input->post();
        if (empty($data['judul']) || empty($data['isi'])) {
            // Mengembalikan response API dalam format JSON
            return json([
                'status' => 400,
                'message' => 'Judul atau isi harus diisi'
            ], 400);
        }

        // Batasi judul menggunakan teks polos
        $data['judul']    = judul($data['judul']);
        $data['tampilan'] = (int) $data['tampilan'];

        $fp          = time();
        // $list_gambar = ['gambar', 'gambar1', 'gambar2', 'gambar3'];

        // foreach ($list_gambar as $gambar) {
        //     $lokasi_file = $_FILES[$gambar]['tmp_name'];
        //     $nama_file   = $fp . '_' . $_FILES[$gambar]['name'];
        //     $nama_file   = trim(str_replace(' ', '_', $nama_file));
        //     if (! empty($lokasi_file)) {
        //         $tipe_file = TipeFile($_FILES[$gambar]);
        //         $hasil     = UploadArtikel($nama_file, $gambar);
        //         if ($hasil) {
        //             $data[$gambar] = $nama_file;
        //         } else {
        //             return json([
        //                 'status' => 400,
        //                 'message' => 'Upload gambar gagal'
        //             ], 400);
        //         }
        //     }
        // }
        $data['id_kategori'] = in_array($cat, Artikel::TIPE_NOT_IN_ARTIKEL) ? null : $cat;
        $data['tipe']        = in_array($cat, Artikel::TIPE_NOT_IN_ARTIKEL) ? $cat : 'dinamis';
        $data['id_user']     = auth()->id;
        // set null id_kategori, artikel tanpa kategori
        if ($data['id_kategori'] == -1) {
            $data['id_kategori'] = null;
        }

        // Kontributor tidak dapat mengaktifkan artikel
        if (auth()->id_grup == 4) {
            $data['enabled'] = StatusEnum::TIDAK;
        }

        // Upload dokumen lampiran
        // $lokasi_file = $_FILES['dokumen']['tmp_name'];
        // $tipe_file   = TipeFile($_FILES['dokumen']);
        // $nama_file   = $_FILES['dokumen']['name'];
        // $ext         = get_extension($nama_file);
        // $nama_file   = time() . random_int(10000, 999999) . $ext;

        // if ($nama_file && ! empty($lokasi_file)) {
        //     if (! in_array($tipe_file, unserialize(MIME_TYPE_DOKUMEN), true) || ! in_array($ext, unserialize(EXT_DOKUMEN))) {
        //         unset($data['link_dokumen']);
        //         return $this->response([
        //             'status' => 400,
        //             'message' => 'Jenis file salah: ' . $tipe_file
        //         ], 400);
        //     } else {
        //         $data['dokumen'] = $nama_file;
        //         if ($data['link_dokumen'] == '') {
        //             $data['link_dokumen'] = $data['judul'];
        //         }
        //         UploadDocument2($nama_file);
        //     }
        // }

        foreach ($list_gambar as $gambar) {
            unset($data['old_' . $gambar]);
        }
        if ($data['tgl_upload'] == '') {
            $data['tgl_upload'] = date('Y-m-d H:i:s');
        } else {
            $tempTgl            = date_create_from_format('d-m-Y H:i:s', $data['tgl_upload']);
            $data['tgl_upload'] = $tempTgl->format('Y-m-d H:i:s');
        }
        if ($data['tgl_agenda'] == '') {
            unset($data['tgl_agenda']);
        } else {
            $tempTgl            = date_create_from_format('d-m-Y H:i:s', $data['tgl_agenda']);
            $data['tgl_agenda'] = $tempTgl->format('Y-m-d H:i:s');
        }

        $data['slug'] = unique_slug('artikel', $data['judul']);

        try {
            $artikel = Artikel::create($data);
            if ($cat == AGENDA) {
                $agenda               = $this->ambil_data_agenda($data);
                $agenda['id_artikel'] = $artikel->id;
                Agenda::create($agenda);
            }
            return json([
                'status' => 200,
                'message' => 'Artikel berhasil ditambahkan',
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return json([
                'status' => 500,
                'message' => 'Artikel gagal ditambahkan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}