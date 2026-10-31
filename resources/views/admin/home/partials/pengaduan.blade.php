<div class="sd-panel">
    <div class="sd-panel-head">
        <h4 class="sd-panel-title"><i class="fa fa-bullhorn"></i> Pengaduan Terbaru</h4>
        <a href="{{ ci_route('pengaduan_admin') }}" class="sd-link-more">Lihat Semua <i class="fa fa-arrow-right"></i></a>
    </div>

    @if ($dash['pengaduan'])
        <div class="sd-status-strip">
            @foreach ($statusPengaduan as $kode => $status)
                <a href="{{ ci_route('pengaduan_admin') }}?status={{ $kode }}" class="sd-status-pill sd-tone-{{ $status['kelas'] }}">
                    <strong>{{ number_format($dash['pengaduan']['status'][$kode] ?? 0, 0, ',', '.') }}</strong>
                    <span>{{ $status['label'] }}</span>
                </a>
            @endforeach
        </div>
    @endif

    <div class="sd-list">
        @forelse (array_slice($dash['pengaduan']['terbaru'] ?? [], 0, 5) as $item)
            <a href="{{ ci_route('pengaduan_admin.detail', $item['id']) }}" class="sd-list-item">
                <span class="sd-list-main">
                    <strong>{{ $item['judul'] }}</strong>
                    <span>{{ $item['nama'] }}@if ($item['waktu']) &middot; <time datetime="{{ $item['waktu']['iso'] }}" title="{{ $item['waktu']['tanggal'] }}">{{ $item['waktu']['relatif'] }}</time>@endif</span>
                </span>
                @php $status = $statusPengaduan[$item['status']] ?? null; @endphp
                @if ($status)
                    <span class="sd-badge sd-badge-{{ $status['kelas'] }}">{{ $status['label'] }}</span>
                @endif
            </a>
        @empty
            @include('admin.home.partials.kosong', ['ikon' => 'fa-bullhorn', 'judul' => 'Belum ada pengaduan', 'teks' => 'Pengaduan dari warga akan muncul di sini.'])
        @endforelse
    </div>
</div>
