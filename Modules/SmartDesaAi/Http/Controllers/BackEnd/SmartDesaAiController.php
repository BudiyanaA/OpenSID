<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

use Modules\SmartDesaAi\Ai\AiProviderException;
use Modules\SmartDesaAi\Ai\AiProviderFactory;
use Modules\SmartDesaAi\Http\Controllers\FiturInternal;
use Modules\SmartDesaAi\Models\SmartDesaAiLog;
use Modules\SmartDesaAi\Services\AiContentService;
use Modules\SmartDesaAi\Services\ArtikelPublisher;
use Modules\SmartDesaAi\Services\BatasPemakaian;
use Modules\SmartDesaAi\Services\Pengaturan;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Admin Web → Smart Desa AI: generator artikel Website Desa.
 *
 * Alur: generate → edit → simpan draft (artikel.enabled = 0) → publish
 * (artikel.enabled = 1). Artikel disimpan di sistem Artikel yang sudah ada.
 */
class SmartDesaAiController extends AdminModulController
{
    // Fitur internal SIMDESA: tanpa pengecekan Paket Tambahan/langganan.
    use FiturInternal;

    public $moduleName      = 'SmartDesaAi';
    public $modul_ini       = 'admin-web';
    public $sub_modul_ini   = 'smart-desa-ai';
    public $aliasController = 'smart_desa_ai';

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
        $status = $this->pengaturan->status();

        return view('smartdesaai::backend.generator', [
            'jenisArtikel'   => AiContentService::jenisArtikel(),
            'gayaArtikel'    => AiContentService::gayaArtikel(),
            'konteksDesa'    => AiContentService::konteksDesa(),
            'kategori'       => ArtikelPublisher::daftarKategori(),
            'siap'           => $this->pengaturan->siapDipakai(),
            'terhubung'      => Pengaturan::STATUS_TERHUBUNG === $status['status'],
            'providerLabel'  => AiProviderFactory::pilihan()[$this->pengaturan->provider()] ?? '',
            'model'          => $this->pengaturan->model(),
            'pemakaian'      => (new BatasPemakaian($this->pengaturan))->ringkasan((int) ci_auth()->id),
            'bolehPublish'   => ArtikelPublisher::bolehPublish(),
            'bolehPengaturan' => can('b', 'smart-desa-ai-pengaturan'),
            'batasInput'     => config('smartdesaai.batas_input', []),
        ]);
    }

    /**
     * Generate artikel (AJAX).
     */
    public function generate(): void
    {
        isCan('u');
        $this->hanyaAjax();

        $userId = (int) ci_auth()->id;

        if (! $this->pengaturan->siapDipakai()) {
            $this->gagal('Smart Desa AI belum diatur. Minta administrator mengisi token API dan model di Pengaturan → Smart Desa AI.');
        }

        try {
            $data = AiContentService::validasi($this->input->post());
        } catch (InvalidArgumentException $e) {
            $this->gagal($e->getMessage());
        }

        $batas = new BatasPemakaian($this->pengaturan);

        if ($pesan = $batas->cek($userId)) {
            $this->gagal($pesan, 429);
        }

        // Cegah generate ganda bersamaan oleh pengguna yang sama.
        $kunci = 'smart_desa_ai_proses_' . identitas('id') . '_' . $userId;

        if (! cache()->add($kunci, 1, 180)) {
            $this->gagal('Permintaan sebelumnya masih diproses. Tunggu sebentar.', 429);
        }

        @set_time_limit(200);

        try {
            $artikel = (new AiContentService($this->pengaturan))->generate($data, $userId);
        } catch (AiProviderException $e) {
            cache()->forget($kunci);
            $this->gagal($e->getMessage(), 200, $batas->ringkasan($userId));
        } catch (Throwable $e) {
            cache()->forget($kunci);
            log_message('error', 'Smart Desa AI generate: ' . get_class($e) . ' di ' . basename($e->getFile()) . ':' . $e->getLine());
            $this->gagal('Terjadi kesalahan saat membuat artikel.');
        }

        cache()->forget($kunci);

        json([
            'sukses'    => true,
            'artikel'   => $artikel,
            'pemakaian' => $batas->ringkasan($userId),
        ]);
    }

    /**
     * Simpan draft / publish ke sistem Artikel yang sudah ada (AJAX).
     */
    public function simpan(): void
    {
        isCan('u');
        $this->hanyaAjax();

        $publish    = 'publish' === $this->input->post('aksi');
        $id         = (int) $this->input->post('id') ?: null;
        $idKategori = (int) $this->input->post('id_kategori') ?: null;

        // Mengubah/menambah artikel mengikuti hak akses modul Artikel.
        if (! can('u', 'artikel')) {
            $this->gagal('Akun Anda tidak memiliki akses untuk menyimpan artikel.', 403);
        }

        try {
            $artikel = (new ArtikelPublisher())->simpan(
                $id,
                (string) $this->input->post('judul'),
                (string) $this->input->post('isi', false),
                $idKategori,
                $publish,
            );
        } catch (InvalidArgumentException $e) {
            $this->gagal($e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'Smart Desa AI simpan: ' . get_class($e) . ' di ' . basename($e->getFile()) . ':' . $e->getLine());
            $this->gagal('Artikel gagal disimpan.');
        }

        SmartDesaAiLog::create([
            'user_id'    => ci_auth()->id,
            'aksi'       => $publish ? SmartDesaAiLog::AKSI_PUBLISH : SmartDesaAiLog::AKSI_DRAFT,
            'provider'   => $this->pengaturan->provider(),
            'model'      => $this->pengaturan->model(),
            'status'     => SmartDesaAiLog::SUKSES,
            'artikel_id' => $artikel->id,
        ]);

        json([
            'sukses'     => true,
            'id'         => $artikel->id,
            'publish'    => (int) $artikel->enabled === 1,
            'pesan'      => $publish ? 'Artikel dipublikasikan dan tampil di Website Desa.' : 'Draft artikel tersimpan (belum tampil di Website Desa).',
            'urlEditor'  => ArtikelPublisher::urlEditor($artikel),
            'urlWebsite' => ArtikelPublisher::urlWebsite($artikel),
        ]);
    }

    private function hanyaAjax(): void
    {
        if (! $this->input->is_ajax_request()) {
            show_404();
        }
    }

    private function gagal(string $pesan, int $status = 200, ?array $pemakaian = null): never
    {
        json(array_filter(['sukses' => false, 'pesan' => $pesan, 'pemakaian' => $pemakaian], static fn ($v) => null !== $v), $status);

        exit;
    }
}
