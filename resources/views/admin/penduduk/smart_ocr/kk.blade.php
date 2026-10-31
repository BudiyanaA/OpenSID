{{-- Smart OCR KK: pratinjau hasil baca KK lalu mengisi field form keluarga existing, tidak menyimpan apa pun. --}}
<div class="sd-ocr" data-ocr="kk" data-form="mainform" data-ocr-base="{{ base_url('assets/js/simdesa/ocr/tesseract/') }}" data-cek-url="{{ ci_route('penduduk.smart_ocr_cek') }}">
    <div class="row">
        <div class="col-sm-8">
            <h4 class="sd-ocr-title"><i class="fa fa-bolt"></i> SMART OCR KK</h4>
            <p class="sd-ocr-desc">Baca Nomor KK, kepala keluarga, dan seluruh anggota dari foto/scan Kartu Keluarga. Diproses di browser, gambar tidak diunggah. Hasil dapat diperiksa &amp; dikoreksi sebelum mengisi form.</p>
        </div>
        <div class="col-sm-4">
            <input type="file" class="hidden sd-ocr-file" form="sd-ocr-noform" accept="image/*">
            <div class="sd-ocr-actions">
                <button type="button" class="btn btn-warning btn-sm btn-block sd-ocr-scan"><i class="fa fa-file-text-o"></i> Scan KK</button>
            </div>
            <div class="sd-ocr-status"></div>
            <div class="sd-ocr-bar"><span></span></div>
            <div class="sd-ocr-preview">
                <div class="sd-ocr-frame"><img alt="Pratinjau KK"></div>
                <div class="btn-group btn-group-xs">
                    <button type="button" class="btn btn-default sd-ocr-rotate" title="Putar 90° lalu baca ulang"><i class="fa fa-repeat"></i> Putar</button>
                    <button type="button" class="btn btn-default sd-ocr-retry" title="Baca ulang"><i class="fa fa-refresh"></i> Ulangi</button>
                    <button type="button" class="btn btn-default sd-ocr-clear" title="Hapus gambar dari layar"><i class="fa fa-trash"></i> Hapus</button>
                </div>
            </div>
        </div>
    </div>
    <div class="sd-ocr-result"></div>
</div>
@include('admin.penduduk.smart_ocr.aset')
