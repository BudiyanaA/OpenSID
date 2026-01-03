<div class="card mb-1 z-index-1">
    <marquee onmouseover="this.stop()" onmouseout="this.start()">
        <div class="teks_berjalan">
            @if (!empty($teks_berjalan))
                @foreach ($teks_berjalan as $teks)
                    <span class="teks small">
                        {{ $teks['teks'] }}
                        @if (!empty($teks['tautan']))
                            <a href="{{ $teks['tautan'] }}" rel="noopener noreferrer" title="Baca Selengkapnya">
                                {{ $teks['judul_tautan'] }}
                            </a>
                        @endif
                    </span>
                @endforeach
            @else
                <span class="teks small">
                    Selamat datang di
                    {{ 
                        ucwords(setting('website_title')) . ' ' .
                        ucwords(setting('sebutan_desa')) . ' ' .
                        ucwords(identitas('nama_desa') ?? '') . ' ' .
                        ucwords(setting('sebutan_kecamatan')) . ' ' .
                        ucwords(identitas('nama_kecamatan') ?? '') . ' ' .
                        ucwords(setting('sebutan_kabupaten')) . ' ' .
                        ucwords(identitas('nama_kabupaten') ?? '') . 
                        ' Provinsi ' . ucwords(identitas('nama_propinsi') ?? '')
                    }}
                </span>
            @endif
        </div>
    </marquee>
</div>
