<?php

use App\Models\Pembangunan as PembangunanModel;

defined('BASEPATH') || exit('No direct script access allowed');

class Admin_pembangunan extends MY_Controller
{
    public function index()
    {
        $pembangunan = PembangunanModel::with(['pembangunanDokumentasi','wilayah']);
        $data= $pembangunan->get();
        return json([
          'status' => 200,
          'data' => $data
        ]);
    }

    public function insert()
    {
      $post               = $this->input->post();
      $data               = $this->validasi($post);
      $data['created_at'] = date('Y-m-d H:i:s');

      if (PembangunanModel::create($data)) {
        return json([
          'status' => 200,
          'data' => $data
        ]);
      }
    }

    private function validasi($post, $id = null, $old_foto = null)
    {
        return [
            'sumber_dana'             => bersihkan_xss($post['sumber_dana']),
            'judul'                   => judul($post['judul']),
            'slug'                    => unique_slug('pembangunan', $post['judul'], $id),
            'volume'                  => bersihkan_xss($post['volume']),
            'waktu'                   => bilangan($post['waktu']),
            'satuan_waktu'            => bilangan($post['satuan_waktu']),
            'tahun_anggaran'          => bilangan($post['tahun_anggaran']),
            'pelaksana_kegiatan'      => bersihkan_xss($post['pelaksana_kegiatan']),
            'id_lokasi'               => $post['lokasi'] ? null : bilangan($post['id_lokasi']),
            'lokasi'                  => $post['id_lokasi'] ? null : $this->security->xss_clean(bersihkan_xss($post['lokasi'])),
            'keterangan'              => $this->security->xss_clean(bersihkan_xss($post['keterangan'])),
            'foto'                    => $this->upload_gambar_pembangunan('foto', $old_foto),
            'anggaran'                => bilangan($post['anggaran']),
            'sumber_biaya_pemerintah' => bilangan($post['sumber_biaya_pemerintah']),
            'sumber_biaya_provinsi'   => bilangan($post['sumber_biaya_provinsi']),
            'sumber_biaya_kab_kota'   => bilangan($post['sumber_biaya_kab_kota']),
            'sumber_biaya_swadaya'    => bilangan($post['sumber_biaya_swadaya']),
            'sumber_biaya_jumlah'     => bilangan($post['sumber_biaya_pemerintah']) + bilangan($post['sumber_biaya_provinsi']) + bilangan($post['sumber_biaya_kab_kota']) + bilangan($post['sumber_biaya_swadaya']),
            'manfaat'                 => $this->security->xss_clean(bersihkan_xss($post['manfaat'])),
            'sifat_proyek'            => bersihkan_xss($post['sifat_proyek']),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];
    }
}