<style>
#footer {
    background-color: #142850;
}
.backTop:hover {
    cursor: pointer;
}
</style>

<div class="backTop z-index-1"
     style="
        position: fixed;
        bottom: 0;
        {{ !empty($desa['nomor_operator'])
            ? 'right: 0; padding: 0.1em; margin: 0.2em; margin-bottom: 5.3em;'
            : 'left: 0; padding: 0.5em; margin: 0.5em;' }}
        display: inline-block;
     ">
    <i class="fa fa-chevron-circle-up {{ !empty($desa['nomor_operator']) ? 'fa-2x' : 'fa-3x' }}"></i>
</div>

<script>
$(window).on('load', function() {
    $('.backTop').hide();
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $(".backTop").fadeIn();
        } else {
            $(".backTop").fadeOut();
        }
    });
    $('.backTop').click(function() {
        $("html, body").animate({scrollTop: 0}, 500);
    });
});
</script>

@if (! setting('inspect_element'))
<script src="{{ asset('js/disabled.min.js') }}"></script>
@endif
