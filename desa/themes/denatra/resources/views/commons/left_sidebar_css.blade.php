@push('style')
<style>
    .pageleft  { float: left; width: 30%; padding-right: 20px; box-sizing: border-box; }
    .pageright { float: right; width: 70%; padding-left: 20px; box-sizing: border-box; }

    @media (max-width: 768px) {
        .pageright,
        .pageleft {
            float: none;
            width: 100%;
            padding: 0;
        }
    }
</style>
@endpush
