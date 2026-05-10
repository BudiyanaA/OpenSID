<?php

use App\Models\Rtm as RtmModel;
use App\Models\Penduduk as Penduduk;

defined('BASEPATH') || exit('No direct script access allowed');

class Rtm extends MY_Controller
{
    public function index()
    {
        $rtm = RtmModel::with(['kepalaKeluarga', 'anggota'])->get();

        return json([
          'status' => 200,
          'data' => $rtm
        ]);
    }

    public function insert()
    {
        $post = $this->input->post();
        $nik  = bilangan($post['nik']);

        $lastRtm = RtmModel::select(['no_kk'])->orderBy(DB::raw('length(no_kk)'), 'desc')->orderBy(DB::raw('no_kk'), 'desc')->first();

        try {

            if ($lastRtm) {
                $noRtm = $lastRtm->no_kk;
                if (strlen($noRtm) >= 5) {
                    // Gunakan 5 digit terakhir sebagai nomor urut
                    $kw           = substr($noRtm, 0, strlen($noRtm) - 5);
                    $noUrut       = substr($noRtm, -5);
                    $noUrut       = str_pad($noUrut + 1, 5, '0', STR_PAD_LEFT);
                    $rtm['no_kk'] = $kw . $noUrut;
                } else {
                    $rtm['no_kk'] = str_pad($noRtm + 1, strlen($noRtm), '0', STR_PAD_LEFT);
                }
            } else {
                $kw           = identitas()->kode_desa;
                $rtm['no_kk'] = $kw . str_pad('1', 5, '0', STR_PAD_LEFT);
            }

            $rtm['nik_kepala']     = $nik;
            $rtm['bdt']            = empty($post['bdt']) ? null : bilangan($post['bdt']);
            $rtm['terdaftar_dtks'] = empty($post['terdaftar_dtks']) ? 0 : 1;
            RtmModel::create($rtm);

            $default['id_rtm']     = $rtm['no_kk'];
            $default['rtm_level']  = 1;
            $default['updated_at'] = date('Y-m-d H:i:s');
            $default['updated_by'] = auth()->id;
            Penduduk::where(['id' => $nik])->update($default);

            // anggota
            $default['rtm_level'] = 2;
            if ($post['anggota_kk']) {
                Penduduk::whereIn('id', $post['anggota_kk'])->update($default);
            }

            return json([
              'status' => 200,
              'data' => $rtm
            ]);
          } catch (Exception  $e) {
            Log::error($e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Rumah Tangga gagal disimpan',
                'error' => $e->getMessage() 
            ], 500);
        }
    }
}