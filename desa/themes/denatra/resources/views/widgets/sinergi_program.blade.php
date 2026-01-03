@php
    $sinergi_program = sinergi_program();
    $perbaris = (int) (setting('gambar_sinergi_program_perbaris') ?: 3);
@endphp

@if ($sinergi_program)
    <style>
        #sinergi_program
        {
            text-align: center;
        }

        #sinergi_program table
        {
            margin: auto;
        }

        #sinergi_program img
        {
            max-width: 100%;
            max-height: 100%;
            transition: all 0.5s;
            -o-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }

        #sinergi_program img:hover
        {
            transition: all 0.3s;
            -o-transition: all 0.3s;
            -moz-transition: all 0.3s;
            -webkit-transition: all 0.3s;
            transform: scale(1.5);
            -moz-transform: scale(1.5);
            -o-transform: scale(1.5);
            -webkit-transform: scale(1.5);
            box-shadow: 2px 2px 6px rgba(0,0,0,0.5);
        }
    </style>

    <div class="card mb-1">
        <div class="card-header border-bottom">
            <div class="media">
                <div class="media-body">
                    <h4 class="content-color-primary mb-0"><i class="material-icons icon-sm">share</i> {{ $judul_widget }}</h4>
                </div>
            </div>
        </div>
        <div class="card-body text-center" id="sinergi_program">
            <table>
            @php
                $totalIterations = count($sinergi_program) + ($perbaris - count($sinergi_program) % $perbaris) % $perbaris;
            @endphp
            @for ($key = 0; $key < $totalIterations; $key++)
                @if ($key % $perbaris === 0)
                    <tr>
                @endif
                @if ($key < count($sinergi_program))
                    <td>
                        <center>
                            <a href="{{ $sinergi_program[$key]['tautan'] }}" target="_blank">
                                <img style="padding: 3px;" src="{{ $sinergi_program[$key]['gambar_url'] }}" alt="Gambar {{ $sinergi_program[$key]['judul'] }}">
                            </a>
                        </center>
                    </td>
                @endif
                @if ($key % $perbaris === $perbaris - 1 || $key === $totalIterations - 1)
                    </tr>
                @endif
            @endfor
            </table>
        </div>
    </div>
@endif
