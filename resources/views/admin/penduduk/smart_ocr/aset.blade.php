@push('css')
    <link rel="stylesheet" href="{{ asset('css/simdesa/smart-ocr.css') }}" />
@endpush
@push('scripts')
    <script src="{{ asset('js/simdesa/ocr/tesseract/tesseract.min.js') }}"></script>
    <script src="{{ asset('js/simdesa/smart-ocr.js') }}"></script>
@endpush
