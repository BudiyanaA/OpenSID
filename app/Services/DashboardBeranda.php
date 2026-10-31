<?php

/*
 * File ini bagian dari:
 *
 * SIMDESA - Sistem Informasi Manajemen Desa
 *
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace App\Services;

use App\Enums\PeristiwaPendudukEnum;
use App\Enums\StatusPengaduanEnum;
use App\Libraries\Keuangan as LibraryKeuangan;
use App\Models\Keuangan;
use App\Models\LogPenduduk;
use App\Models\LogSurat;
use App\Models\Pembangunan;
use App\Models\Pengaduan;
use App\Models\PermohonanSurat;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Data tampilan dashboard (Beranda) admin.
 *
 * Hanya MEMBACA data yang sudah ada melalui model, service, dan library
 * existing (LaporanPenduduk, Library Keuangan, dst). Tidak ada penulisan
 * data. Setiap bagian dibungkus try/catch: bila gagal, bagian tersebut
 * bernilai null dan view menampilkan empty state, sehingga beranda tidak
 * pernah rusak karena satu sumber data.
 *
 * Hasil di-cache singkat per desa agar beranda tetap ringan.
 */
class DashboardBeranda
{
    private const CACHE_TTL = 300;

    public function data(): array
    {
        $key = 'simdesa_dashboard_beranda_v2_' . identitas('id');

        return cache()->remember($key, self::CACHE_TTL, fn (): array => [
            'penduduk'    => $this->aman(fn () => $this->statistikPenduduk()),
            'layanan'     => $this->aman(fn () => $this->statistikLayanan()),
            'pengaduan'   => $this->aman(fn () => $this->pengaduan()),
            'aktivitas'   => $this->aman(fn () => $this->aktivitas()),
            'pembangunan' => $this->aman(fn () => $this->pembangunan()),
            'keuangan'    => $this->aman(fn () => $this->keuangan()),
        ]);
    }

    /**
     * Komposisi jenis kelamin dan rentang umur penduduk aktif.
     * Memakai perhitungan yang sama dengan menu Statistik > Rentang Umur.
     */
    private function statistikPenduduk(): array
    {
        $laporan = (new LaporanPenduduk())->listData(13);
        $total   = $laporan['total'] ?? [];
        unset($laporan['total']);

        $umur = collect($laporan)
            ->reject(static fn ($row) => in_array(data_get($row, 'id'), [JUMLAH, BELUM_MENGISI], false))
            ->map(static fn ($row) => [
                'nama'      => (string) data_get($row, 'nama'),
                'laki'      => (int) data_get($row, 'laki'),
                'perempuan' => (int) data_get($row, 'perempuan'),
            ])
            ->values()
            ->all();

        return [
            'laki'      => (int) data_get($total, 'laki'),
            'perempuan' => (int) data_get($total, 'perempuan'),
            'jumlah'    => (int) data_get($total, 'jumlah'),
            'umur'      => $umur,
        ];
    }

    /**
     * Jumlah surat layanan per bulan selama 12 bulan terakhir (arsip layanan).
     */
    private function statistikLayanan(): array
    {
        $mulai = Carbon::now()->startOfMonth()->subMonths(11);

        $perBulan = LogSurat::without(['formatSurat', 'penduduk', 'pamong', 'tolak'])
            ->whereNull('deleted_at')
            ->where('tanggal', '>=', $mulai)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as jumlah")
            // Kelompokkan dengan ekspresi yang sama (kompatibel sql_mode ONLY_FULL_GROUP_BY)
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->pluck('jumlah', 'bulan');

        $bulan = [];

        for ($i = 0; $i < 12; $i++) {
            $tgl     = $mulai->copy()->addMonths($i);
            $bulan[] = [
                'label'  => $tgl->locale('id')->translatedFormat('M y'),
                'jumlah' => (int) ($perBulan[$tgl->format('Y-m')] ?? 0),
            ];
        }

        return [
            'bulan' => $bulan,
            'total' => array_sum(array_column($bulan, 'jumlah')),
        ];
    }

    private function pengaduan(): array
    {
        $status = Pengaduan::without('child')->tipe()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $terbaru = Pengaduan::without('child')->tipe()
            ->latest('created_at')
            ->take(5)
            ->get(['id', 'judul', 'nama', 'status', 'created_at'])
            ->map(static fn ($item) => [
                'id'     => $item->id,
                'judul'  => (string) $item->judul,
                'nama'   => (string) $item->nama,
                'status' => (int) $item->status,
                'waktu'  => self::waktu($item->getRawOriginal('created_at')),
            ])
            ->all();

        return [
            'terbaru' => $terbaru,
            'status'  => [
                StatusPengaduanEnum::MENUNGGU_DIPROSES => (int) ($status[StatusPengaduanEnum::MENUNGGU_DIPROSES] ?? 0),
                StatusPengaduanEnum::SEDANG_DIPROSES   => (int) ($status[StatusPengaduanEnum::SEDANG_DIPROSES] ?? 0),
                StatusPengaduanEnum::SELESAI_DIPROSES  => (int) ($status[StatusPengaduanEnum::SELESAI_DIPROSES] ?? 0),
            ],
        ];
    }

    /**
     * Gabungan catatan terbaru dari log yang sudah ada: surat, peristiwa
     * penduduk, permohonan layanan mandiri, dan pengaduan. `akses` dipakai
     * view untuk menyaring sesuai hak akses pengguna.
     */
    private function aktivitas(): array
    {
        $items = [];

        $this->aman(static function () use (&$items): void {
            LogSurat::with(['formatSurat', 'penduduk'])->whereNull('deleted_at')->latest('tanggal')->take(5)->get()
                ->each(static function ($log) use (&$items): void {
                    $items[] = [
                        'akses' => 'arsip-layanan',
                        'ikon'  => 'fa-envelope-o',
                        'warna' => 'primary',
                        'judul' => 'Surat ' . ($log->formatSurat->nama ?? 'layanan') . ' dibuat',
                        'rinci' => $log->penduduk->nama ?? null,
                        'waktu' => self::waktu($log->getRawOriginal('tanggal')),
                    ];
                });
        });

        $this->aman(static function () use (&$items): void {
            $label = PeristiwaPendudukEnum::labels();
            LogPenduduk::with('penduduk')->latest('tgl_lapor')->take(5)->get()
                ->each(static function ($log) use (&$items, $label): void {
                    $items[] = [
                        'akses' => 'peristiwa',
                        'ikon'  => 'fa-user',
                        'warna' => 'success',
                        'judul' => 'Peristiwa ' . Str::lower($label[$log->kode_peristiwa] ?? 'penduduk') . ' dicatat',
                        'rinci' => $log->penduduk->nama ?? null,
                        'waktu' => self::waktu($log->getRawOriginal('tgl_lapor')),
                    ];
                });
        });

        $this->aman(static function () use (&$items): void {
            PermohonanSurat::with(['surat', 'penduduk'])->latest('created_at')->take(5)->get()
                ->each(static function ($permohonan) use (&$items): void {
                    $items[] = [
                        'akses' => 'permohonan-surat',
                        'ikon'  => 'fa-mobile',
                        'warna' => 'purple',
                        'judul' => 'Permohonan layanan mandiri masuk',
                        'rinci' => trim(($permohonan->surat->nama ?? '') . ($permohonan->penduduk ? ' · ' . $permohonan->penduduk->nama : ''), ' ·'),
                        'waktu' => self::waktu($permohonan->getRawOriginal('created_at')),
                    ];
                });
        });

        $this->aman(static function () use (&$items): void {
            Pengaduan::without('child')->tipe()->latest('created_at')->take(5)->get(['judul', 'nama', 'created_at'])
                ->each(static function ($pengaduan) use (&$items): void {
                    $items[] = [
                        'akses' => 'pengaduan',
                        'ikon'  => 'fa-bullhorn',
                        'warna' => 'warning',
                        'judul' => 'Pengaduan warga diterima',
                        'rinci' => $pengaduan->judul,
                        'waktu' => self::waktu($pengaduan->getRawOriginal('created_at')),
                    ];
                });
        });

        return collect($items)
            ->filter(static fn ($item) => $item['waktu'] !== null)
            ->sortByDesc(static fn ($item) => $item['waktu']['iso'])
            ->values()
            ->all();
    }

    private function pembangunan(): array
    {
        $query = Pembangunan::with(['pembangunanDokumentasi', 'wilayah']);

        $total = (clone $query)->count();

        $proyek = $query->orderByDesc('tahun_anggaran')->orderByDesc('id')->take(4)->get()
            ->map(static function ($item): array {
                $progres = $item->pembangunanDokumentasi
                    ->map(static fn ($dok): int => (int) str_replace('%', '', (string) $dok->persentase))
                    ->max();

                return [
                    'judul'       => (string) $item->judul,
                    'lokasi'      => $item->lokasi ?: ($item->wilayah ? trim('Dusun ' . $item->wilayah->dusun) : null),
                    'anggaran'    => (float) $item->anggaran,
                    'sumber_dana' => is_array($item->sumber_dana) ? implode(', ', array_filter($item->sumber_dana)) : $item->sumber_dana,
                    'tahun'       => $item->tahun_anggaran,
                    'aktif'       => (int) $item->status === 1,
                    'progres'     => $progres === null ? null : max(0, min(100, $progres)),
                ];
            })
            ->all();

        return ['total' => $total, 'proyek' => $proyek];
    }

    /**
     * Ringkasan APBDes tahun anggaran terbaru, memakai perhitungan yang sama
     * dengan menu Keuangan > Laporan (grafik pelaksanaan APBDes).
     */
    private function keuangan(): ?array
    {
        $tahun = Keuangan::tahunAnggaran()->first()?->tahun;

        if (! $tahun) {
            return null;
        }

        $grafik      = (new LibraryKeuangan())->grafik_keuangan_tema($tahun);
        $pelaksanaan = collect($grafik['data_widget']['res_pelaksanaan'] ?? [])
            ->filter(static fn ($item, $key) => $key !== 'laporan' && is_array($item))
            ->mapWithKeys(static fn ($item) => [Str::upper((string) $item['judul']) => [
                'judul'     => Str::title((string) $item['judul']),
                'anggaran'  => (float) ($item['anggaran'] ?? 0),
                'realisasi' => (float) ($item['realisasi'] ?? 0),
                'persen'    => round((float) ($item['persen'] ?? 0), 1),
            ]]);

        if ($pelaksanaan->isEmpty() || $pelaksanaan->sum('anggaran') == 0) {
            return null;
        }

        return [
            'tahun'       => $tahun,
            'pendapatan'  => $pelaksanaan->get('PENDAPATAN'),
            'belanja'     => $pelaksanaan->get('BELANJA'),
            'pelaksanaan' => $pelaksanaan->values()->all(),
        ];
    }

    private static function waktu(?string $tanggal): ?array
    {
        if (empty($tanggal) || str_starts_with($tanggal, '0000')) {
            return null;
        }

        $carbon = Carbon::parse($tanggal)->locale('id');

        return [
            'iso'      => $carbon->toIso8601String(),
            'relatif'  => $carbon->diffForHumans(),
            'tanggal'  => $carbon->translatedFormat('d M Y, H:i'),
        ];
    }

    private function aman(callable $callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            log_message('error', 'Dashboard beranda: ' . $e->getMessage());

            return null;
        }
    }
}
