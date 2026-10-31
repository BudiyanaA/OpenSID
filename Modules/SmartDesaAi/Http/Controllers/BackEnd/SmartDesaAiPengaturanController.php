<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

use Modules\SmartDesaAi\Ai\AiProviderException;
use Modules\SmartDesaAi\Ai\AiProviderFactory;
use Modules\SmartDesaAi\Http\Controllers\FiturInternal;
use Modules\SmartDesaAi\Models\SmartDesaAiLog;
use Modules\SmartDesaAi\Services\BatasPemakaian;
use Modules\SmartDesaAi\Services\Pengaturan;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pengaturan → Smart Desa AI.
 *
 * Token API bersifat write-only: diterima dari form, disimpan terenkripsi,
 * dan tidak pernah dikirim kembali ke browser (hanya versi tersamar).
 */
class SmartDesaAiPengaturanController extends AdminModulController
{
    // Fitur internal SIMDESA: tanpa pengecekan Paket Tambahan/langganan.
    use FiturInternal;

    public $moduleName      = 'SmartDesaAi';
    public $modul_ini       = 'pengaturan';
    public $sub_modul_ini   = 'smart-desa-ai-pengaturan';
    public $aliasController = 'smart_desa_ai_pengaturan';

    private Pengaturan $pengaturan;

    public function __construct()
    {
        parent::__construct();
        isCan('b');

        $this->pengaturan = new Pengaturan();

        if (! $this->pengaturan->terpasang()) {
            redirect_with('error', 'Modul Smart Desa AI belum dipasang (migrasi belum dijalankan).', 'beranda');
        }
    }

    public function index()
    {
        $userId = (int) ci_auth()->id;

        return view('smartdesaai::backend.pengaturan', [
            'pilihanProvider' => AiProviderFactory::pilihan(),
            'provider'        => $this->pengaturan->provider(),
            'model'           => $this->pengaturan->model(),
            'daftarModel'     => $this->pengaturan->daftarModel(),
            'status'          => $this->pengaturan->status(),
            'tokenTersamar'   => $this->pengaturan->tokenTersamar(),
            'batasJam'        => $this->pengaturan->batasPerUserPerJam(),
            'batasHari'       => $this->pengaturan->batasHarian(),
            'pemakaian'       => (new BatasPemakaian($this->pengaturan))->ringkasan($userId),
            'statistik'       => $this->statistikBulanIni(),
            'modelBawaan'     => array_map(static fn ($k) => AiProviderFactory::modelBawaan($k), array_combine(array_keys(AiProviderFactory::PENYEDIA), array_keys(AiProviderFactory::PENYEDIA))),
        ]);
    }

    public function update(): void
    {
        isCan('u');

        $provider  = (string) $this->input->post('provider');
        $model     = trim((string) $this->input->post('model'));
        $token     = (string) $this->input->post('token', false);
        $batasJam  = (int) $this->input->post('batas_user_jam');
        $batasHari = (int) $this->input->post('batas_harian');

        if (! AiProviderFactory::valid($provider)) {
            redirect_with('error', 'Penyedia AI tidak valid.', ci_route('smart_desa_ai_pengaturan'));
        }

        if ('' === $model) {
            $model = AiProviderFactory::modelBawaan($provider);
        }

        if ('' !== $model && ! preg_match('/^[A-Za-z0-9._:\/-]{1,100}$/', $model)) {
            redirect_with('error', 'ID model tidak valid.', ci_route('smart_desa_ai_pengaturan'));
        }

        if ($batasJam < 1 || $batasJam > 1000 || $batasHari < 1 || $batasHari > 10000) {
            redirect_with('error', 'Batas pemakaian tidak valid (per jam 1–1000, per hari 1–10000).', ci_route('smart_desa_ai_pengaturan'));
        }

        $token = trim($token);

        if ('' !== $token && (mb_strlen($token) < 20 || mb_strlen($token) > 400 || preg_match('/\s/', $token))) {
            redirect_with('error', 'Format token API tidak valid.', ci_route('smart_desa_ai_pengaturan'));
        }

        $this->pengaturan->simpanUmum($provider, $model, $batasJam, $batasHari);

        if ('' !== $token) {
            $this->pengaturan->simpanToken($token);
        }

        unset($token);

        redirect_with('success', 'Pengaturan Smart Desa AI berhasil disimpan. Jalankan "Test Koneksi" untuk memastikan token dan model dapat dipakai.', ci_route('smart_desa_ai_pengaturan'));
    }

    /**
     * Test Koneksi (AJAX): ambil daftar model dengan token tersimpan.
     */
    public function uji(): void
    {
        isCan('u');

        if (! $this->input->is_ajax_request()) {
            show_404();
        }

        $provider = $this->pengaturan->provider();
        $mulai    = microtime(true);

        try {
            $daftar = AiProviderFactory::buat($provider, $this->pengaturan->tokenAsli(), 30.0)->listModels();

            $this->pengaturan->catatStatus(Pengaturan::STATUS_TERHUBUNG, $daftar);
            $this->catatLog($provider, SmartDesaAiLog::SUKSES, null, $mulai);

            json([
                'sukses' => true,
                'pesan'  => sprintf('Terhubung ke %s. %d model tersedia.', AiProviderFactory::pilihan()[$provider], count($daftar)),
                'models' => $daftar,
                'model'  => $this->pengaturan->model(),
                'waktu'  => tgl_indo2(date('Y-m-d H:i:s')),
            ]);
        } catch (AiProviderException $e) {
            if (AiProviderException::TOKEN_KOSONG !== $e->kode) {
                $this->pengaturan->catatStatus(Pengaturan::STATUS_GAGAL);
                $this->catatLog($provider, SmartDesaAiLog::GAGAL, $e->kode, $mulai);
            }

            json(['sukses' => false, 'pesan' => $e->getMessage()], 200);
        } catch (Throwable $e) {
            log_message('error', 'Smart Desa AI uji koneksi: ' . get_class($e) . ' di ' . basename($e->getFile()) . ':' . $e->getLine());
            json(['sukses' => false, 'pesan' => 'Terjadi kesalahan saat menguji koneksi.'], 200);
        }
    }

    public function hapusToken(): void
    {
        isCan('h');

        $this->pengaturan->hapusToken();

        redirect_with('success', 'Token API berhasil dihapus.', ci_route('smart_desa_ai_pengaturan'));
    }

    private function catatLog(string $provider, string $status, ?string $kodeError, float $mulai): void
    {
        SmartDesaAiLog::create([
            'user_id'    => ci_auth()->id,
            'aksi'       => SmartDesaAiLog::AKSI_UJI,
            'provider'   => $provider,
            'model'      => $this->pengaturan->model(),
            'status'     => $status,
            'kode_error' => $kodeError,
            'durasi_ms'  => (int) round((microtime(true) - $mulai) * 1000),
        ]);
    }

    /**
     * @return array{generate: int, gagal: int, publish: int, input_tokens: int, output_tokens: int}
     */
    private function statistikBulanIni(): array
    {
        $awal = date('Y-m-01 00:00:00');

        $generate = SmartDesaAiLog::generate()->where('created_at', '>=', $awal);

        return [
            'generate'      => (clone $generate)->where('status', SmartDesaAiLog::SUKSES)->count(),
            'gagal'         => (clone $generate)->where('status', SmartDesaAiLog::GAGAL)->count(),
            'publish'       => SmartDesaAiLog::where('aksi', SmartDesaAiLog::AKSI_PUBLISH)->where('created_at', '>=', $awal)->count(),
            'input_tokens'  => (int) (clone $generate)->sum('input_tokens'),
            'output_tokens' => (int) (clone $generate)->sum('output_tokens'),
        ];
    }
}
