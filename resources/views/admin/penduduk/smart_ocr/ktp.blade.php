{{-- Smart OCR KTP: hanya mengisi field form biodata existing, tidak menyimpan apa pun. --}}
{{-- data-debug-allowed="1": panel debug aktif bila URL memuat ?ocr_debug=1 (hanya pengembangan). Set "0" untuk menonaktifkan. --}}
<div class="sd-ocr" data-ocr="ktp" data-form="mainform" data-debug-allowed="1" data-ocr-base="{{ base_url('assets/js/simdesa/ocr/tesseract/') }}" data-cek-url="{{ ci_route('penduduk.smart_ocr_cek') }}">
    <h4 class="sd-ocr-title"><i class="fa fa-bolt"></i> SMART OCR KTP</h4>
    <p class="sd-ocr-desc">Isi otomatis biodata dari foto KTP. Diproses di browser, gambar tidak diunggah.</p>
    <input type="file" class="hidden sd-ocr-file" form="sd-ocr-noform" accept="image/*">
    <div class="sd-ocr-actions">
        <button type="button" class="btn btn-warning btn-sm btn-block sd-ocr-scan"><i class="fa fa-id-card"></i> Scan KTP</button>
    </div>
    <div class="sd-ocr-status"></div>
    <div class="sd-ocr-bar"><span></span></div>
    <div class="sd-ocr-preview">
        <div class="sd-ocr-frame"><img alt="Pratinjau KTP"></div>
        <div class="btn-group btn-group-xs">
            <button type="button" class="btn btn-default sd-ocr-rotate" title="Putar 90° lalu baca ulang"><i class="fa fa-repeat"></i> Putar</button>
            <button type="button" class="btn btn-default sd-ocr-retry" title="Baca ulang"><i class="fa fa-refresh"></i> Ulangi</button>
            <button type="button" class="btn btn-default sd-ocr-clear" title="Hapus gambar dari layar"><i class="fa fa-trash"></i> Hapus</button>
        </div>
    </div>
    <div class="sd-ocr-result"></div>
</div>
@include('admin.penduduk.smart_ocr.aset')
