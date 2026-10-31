<div class="sd-state sd-panel-empty">
    <span class="sd-state-icon"><i class="fa {{ $ikon ?? 'fa-inbox' }}"></i></span>
    <p class="sd-state-title">{{ $judul }}</p>
    @isset($teks)
        <p class="sd-state-text">{{ $teks }}</p>
    @endisset
</div>
