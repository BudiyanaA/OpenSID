@push('style')
<style>
    .fb_iframe_widget_fluid_desktop iframe {
        width: 100%;
    }
</style>
@endpush

<div class="card-body border-bottom mt-0">
    <div class="row text-center">
        <div class="col no-gutters content-color-secondary small">
            <a name="fb_share"
                href="http://www.facebook.com/sharer.php?u={{ $link }}"
                onclick='window.open(this.href,"popupwindow","status=0,height=500,width=500,resizable=0,top=50,left=100");return false;'
                rel='noopener noreferrer' target='_blank' title='Facebook'>
                <button type="button" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="27" fill="currentColor" viewBox="0 0 24 24">
                        <path d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z"/>
                    </svg>
                </button>
            </a>
            <a href="http://twitter.com/share?source=sharethiscom&url={{ $link }}&text={{ $judul }}"
                onclick='window.open(this.href,"popupwindow","status=0,height=500,width=500,resizable=0,top=50,left=100");return false;'
                rel='noopener noreferrer' target='_blank' title='Twitter'>
                <button type="button" class="btn btn-dark btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="27" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </button>
            </a>
            <a href="https://telegram.me/share/url?url={{ $link }}&text={{ $judul }}"
                onclick='window.open(this.href,"popupwindow","status=0,height=500,width=500,resizable=0,top=50,left=100");return false;'
                rel='noopener noreferrer' target='_blank' title='Telegram'>
                <button type="button" class="btn btn-info btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="27" fill="currentColor" viewBox="0 0 24 24">
                        <path d="m9.417 15.181-.397 5.584c.568 0 .814-.244 1.109-.537l2.663-2.545 5.518 4.041c1.012.564 1.725.267 1.998-.931l3.622-16.972.001-.001c.321-1.496-.541-2.081-1.527-1.714l-21.29 8.151c-1.453.564-1.431 1.374-.247 1.741l5.443 1.693 12.643-7.911c.595-.394 1.136-.176.691.218z"/>
                    </svg>
                </button>
            </a>
            <a href="https://api.whatsapp.com/send?text={{ $link }}%0A{{ $judul }}"
                onclick='window.open(this.href,"popupwindow","status=0,height=500,width=500,resizable=0,top=50,left=100");return false;'
                rel='noopener noreferrer' target='_blank' title='Whatsapp'>
                <button type="button" class="btn btn-success btn-sm">
                    <i class="fa fa-whatsapp fa-2x"></i>
                </button>
            </a>
            <a href="mailto:?subject={{ $judul }}&body=Selengkapnya di {{ $link }}" title='Email'>
                <button type="button" class="btn btn-danger btn-sm">
                    <i class="fa fa-envelope fa-2x"></i>
                </button>
            </a>
            <a href="javascript:void(0);" onclick="printDiv('printableArea')" title='Cetak'>
                <button type="button" class="btn btn-warning btn-sm">
                    <i class="fa fa-print fa-2x"></i>
                </button>
            </a>
        </div>
    </div>
</div>
