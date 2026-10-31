<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Services;

use App\Models\SettingAplikasi;
use App\Repositories\SettingAplikasiRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Modules\SmartDesaAi\Ai\AiProviderFactory;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Akses pengaturan Smart Desa AI milik instalasi desa ini.
 *
 * Token API:
 * - disimpan terenkripsi (encrypter Laravel, kunci desa/app_key),
 * - hanya didekripsi di server tepat sebelum memanggil penyedia AI,
 * - ditulis lewat query builder agar TIDAK tercatat di log aktivitas
 *   (model SettingAplikasi mencatat semua perubahan nilai),
 * - tidak pernah dikirim ke view; view hanya menerima versi tersamar.
 */
class Pengaturan
{
    public const KEY_PROVIDER    = 'smart_desa_ai_provider';
    public const KEY_MODEL       = 'smart_desa_ai_model';
    public const KEY_TOKEN       = 'smart_desa_ai_token';
    public const KEY_STATUS      = 'smart_desa_ai_status';
    public const KEY_MODELS      = 'smart_desa_ai_models';
    public const KEY_BATAS_JAM   = 'smart_desa_ai_batas_user_jam';
    public const KEY_BATAS_HARI  = 'smart_desa_ai_batas_harian';

    public const STATUS_TERHUBUNG = 'terhubung';
    public const STATUS_GAGAL     = 'gagal';
    public const STATUS_BELUM     = 'belum';

    private SettingAplikasiRepository $repo;

    public function __construct()
    {
        $this->repo = new SettingAplikasiRepository();
    }

    public function terpasang(): bool
    {
        return null !== $this->repo->firstByKey(self::KEY_PROVIDER);
    }

    public function provider(): string
    {
        $kode = (string) $this->nilai(self::KEY_PROVIDER);

        return AiProviderFactory::valid($kode) ? $kode : 'claude';
    }

    public function model(): string
    {
        return (string) $this->nilai(self::KEY_MODEL);
    }

    public function batasPerUserPerJam(): int
    {
        return max(1, (int) ($this->nilai(self::KEY_BATAS_JAM) ?: 10));
    }

    public function batasHarian(): int
    {
        return max(1, (int) ($this->nilai(self::KEY_BATAS_HARI) ?: 50));
    }

    /**
     * @return list<string>
     */
    public function daftarModel(): array
    {
        $data = json_decode((string) $this->nilai(self::KEY_MODELS), true);

        return is_array($data) ? array_values(array_filter($data, 'is_string')) : [];
    }

    /**
     * @return array{status: string, waktu: ?string, provider: ?string}
     */
    public function status(): array
    {
        $data = json_decode((string) $this->nilai(self::KEY_STATUS), true);

        $status = [
            'status'   => $data['status'] ?? self::STATUS_BELUM,
            'waktu'    => $data['waktu'] ?? null,
            'provider' => $data['provider'] ?? null,
        ];

        // Status hanya berlaku untuk penyedia & token yang sedang tersimpan.
        if (! $this->adaToken() || $status['provider'] !== $this->provider()) {
            $status['status'] = self::STATUS_BELUM;
        }

        return $status;
    }

    public function siapDipakai(): bool
    {
        return $this->adaToken() && '' !== $this->model();
    }

    public function simpanUmum(string $provider, string $model, int $batasJam, int $batasHari): void
    {
        $providerLama = $this->provider();

        $this->ubah(self::KEY_PROVIDER, $provider);
        $this->ubah(self::KEY_MODEL, $model);
        $this->ubah(self::KEY_BATAS_JAM, (string) $batasJam);
        $this->ubah(self::KEY_BATAS_HARI, (string) $batasHari);

        if ($providerLama !== $provider) {
            // Daftar model milik penyedia lama tidak berlaku lagi.
            $this->ubah(self::KEY_MODELS, '[]');
        }
    }

    public function catatStatus(string $status, array $daftarModel = []): void
    {
        $this->ubah(self::KEY_STATUS, json_encode([
            'status'   => $status,
            'waktu'    => date('Y-m-d H:i:s'),
            'provider' => $this->provider(),
        ]));

        if (self::STATUS_TERHUBUNG === $status) {
            $this->ubah(self::KEY_MODELS, json_encode(array_values($daftarModel)));
        }
    }

    // ---------------------------------------------------------------- token

    public function adaToken(): bool
    {
        return '' !== (string) $this->nilaiMentah(self::KEY_TOKEN);
    }

    /**
     * Versi tersamar untuk ditampilkan, mis. "••••••••a1B2".
     */
    public function tokenTersamar(): ?string
    {
        $token = $this->tokenAsli();

        if ('' === $token) {
            return null;
        }

        return str_repeat('•', 8) . (mb_strlen($token) > 12 ? mb_substr($token, -4) : '');
    }

    /**
     * Token asli. HANYA untuk dipakai server saat memanggil penyedia AI.
     */
    public function tokenAsli(): string
    {
        $tersimpan = (string) $this->nilaiMentah(self::KEY_TOKEN);

        if ('' === $tersimpan) {
            return '';
        }

        try {
            return (string) app('encrypter')->decrypt($tersimpan, false);
        } catch (DecryptException) {
            // Kunci desa/app_key berubah atau data rusak: anggap belum diatur.
            return '';
        }
    }

    public function simpanToken(string $token): void
    {
        $this->tulisTanpaLog(self::KEY_TOKEN, app('encrypter')->encrypt(trim($token), false));
        $this->ubah(self::KEY_STATUS, '');
    }

    public function hapusToken(): void
    {
        $this->tulisTanpaLog(self::KEY_TOKEN, '');
        $this->ubah(self::KEY_STATUS, '');
        $this->ubah(self::KEY_MODELS, '[]');
    }

    // ------------------------------------------------------------- internal

    private function nilai(string $key): ?string
    {
        return $this->repo->firstByKey($key)?->value;
    }

    /**
     * Baca langsung dari database tanpa model cache/objek setting bersama.
     */
    private function nilaiMentah(string $key): ?string
    {
        return SettingAplikasi::query()->withoutGlobalScopes()
            ->where('config_id', identitas('id'))
            ->where('key', $key)
            ->toBase()
            ->value('value');
    }

    private function ubah(string $key, string $value): void
    {
        if ($this->repo->firstByKey($key)) {
            $this->repo->updateWithKey($key, $value);
        }
    }

    /**
     * Tulis nilai lewat query builder: tidak memicu event model sehingga
     * tidak tercatat di log aktivitas.
     */
    private function tulisTanpaLog(string $key, string $value): void
    {
        SettingAplikasi::query()->withoutGlobalScopes()
            ->where('config_id', identitas('id'))
            ->where('key', $key)
            ->toBase()
            ->update(['value' => $value]);

        $this->repo->flushCache();
        cache()->forget('setting_aplikasi');
    }
}
