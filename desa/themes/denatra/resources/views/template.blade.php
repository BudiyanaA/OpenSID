@includeIf('theme::commons.constant')

<!DOCTYPE html>
<html lang="en">
<head>
@php
	$segment1 = request()->segment(1);
	$segment2 = request()->segment(2);
	function extract_youtube_id($url)
	{
		$pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
		if (preg_match($pattern, $url, $matches)) {
			return $matches[1];
		}
		return '';
	}

	function hr($tgl)
	{
		$daftar_hari = [
			'Sunday' => 'Minggu', 
			'Monday' => 'Senin', 
			'Tuesday' => 'Selasa', 
			'Wednesday' => 'Rabu', 
			'Thursday' => 'Kamis', 
			'Friday' => 'Jumat', 
			'Saturday' => 'Sabtu'
		];
		$namahari = date('l', strtotime($tgl));
		$hari = $daftar_hari[$namahari] . ", " . tgl_indo2($tgl);
		return $hari;
	}

	$desa_title = setting('website_title') . ' ' . trim(ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa']);
	$desa_nama = trim(ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa']);
	$desa_wilayah = ucwords(setting('sebutan_kecamatan')) . ' ' . $desa['nama_kecamatan'] . ' ' .
					ucwords(setting('sebutan_kabupaten')) . ' ' . $desa['nama_kabupaten'] .
					' Provinsi ' . $desa['nama_propinsi'];
@endphp
	@includeIf('theme::commons.meta')
	@includeIf('theme::commons.source_css')
	@includeIf('theme::commons.source_js')
	@stack('style')
</head>

<body class="fixed-header sidebar-right-close sidebar-left-close">
	{{-- @includeIf('theme::commons.loader') --}}
	<div class="wrapper">
		@includeIf('theme::commons.header')
		@includeIf('theme::commons.sidebar')
		<div class="container{{ cekFluid() ? '-fluid' : '' }} {{ in_array(request()->segment(1), ['', 'first', 'index']) ? '' : 'mt-1' }} main-container @if(empty($cari) && in_array(request()->segment(1), ['', 'first', 'index']) && !theme_config('menu', true)) blog-content @endif">
			@includeIf('theme::commons.teks_berjalan')
			@if(in_array(request()->segment(1), ['']))
			@includeIf('theme::commons.slider')
			@if(theme_config('jadwal_sholat', true))
			@includeIf('theme::partials.home.jadwal-shalat')
			@endif
			@includeIf('theme::partials.home.banner')
			@endif
			@includeIf('theme::partials.home.informasi')
			@includeIf('theme::partials.home.feed')
			<div class="row">
				<div class="col-lg-12 col-md-12">
					@yield('layout')
					@if ($artikel || $segment1 === null || $segment2 === 'first')
					@includeIf('theme::partials.module_home')
					@endif
				</div>
				@if (
					in_array(request()->segment(1), ['', 'first', 'index']) &&
					(
						$single_artikel['tampilan'] == 1 ||
						($layout == 'right-widget' && $layout != 'full-width' &&
							!in_array($single_artikel['tampilan'], [2, 3])
						)
					)
				)
					<div class="col-md-4 col-lg-4">
						@includeIf('theme::partials.home.widgets')
					</div>
				@endif
			</div>
		</div>
		@if (!is_null($transparansi))
		@includeIf('theme::partials.home.apbdesa-tema', $transparansi)
		@endif
	</div>
	<footer id="footer" class="border-top">
		<div class="row">
			<div class="col-12 text-white text-center">
				&copy; <a href="{{ site_url() }}" class="text-white" rel="noopener noreferrer" target="_blank">{{ ucwords(setting('sebutan_pemerintah_desa')) }} {{ $desa['nama_desa'] ? ucwords(' ' . $desa['nama_desa']) : '' }}</a>
				<br><a href="https://www.ariandi.net" class="text-white" rel="noopener noreferrer" title="{{ date('d F Y', strtotime(THEME_TIMESTAMP)) }}" target="_blank">Tema {{ THEME_NAME }} {{ THEME_VERSION }}</a>
				@if (file_exists('diskominfo'))
					<a href="{{ config('diskominfo') ? config('diskominfo') : '#' }}" rel="noopener noreferrer" target="_blank">
						<img src="{{ theme_asset('images/diskominfo.png') }}" style="width: 55px;" alt="" />
					</a>
				@endif
				@if (file_exists('hosting'))
					<a href="https://member.jagoanhosting.com/aff.php?aff=7056" rel="noopener noreferrer" target="_blank">
						<img src="{{ theme_asset('images/hosting.png') }}" style="width: 55px;" alt="" />
					</a>
				@endif
				@if (file_exists('mitra'))
					<a href="https://my.idcloudhost.com/aff.php?aff=3172" rel="noopener noreferrer" target="_blank" class="text-white">
						<img src="{{ asset('images/Logo-IDcloudhost.png') }}" height="15px" alt="">
					</a>
				@endif
				@if (setting('tte'))
					<img src="{{ asset('images/bsre.png?v') }}" class="img-responsive" style="width: 55px;" alt="" />
				@endif
			</div>
			<div class="col-12 col-sm-6 text-right"></div>
		</div>
	</footer>

	@if (!empty($desa['nomor_operator']))
	<div id="ChatSupport"></div>
	<script src="{{ theme_asset('plugin/components/moment/moment.min.js') }}"></script>
	<script src="{{ theme_asset('plugin/components/moment/moment-timezone-with-data.min.js') }}"></script>
	<script src="{{ theme_asset('plugin/czm-chat-support.min.js') }}"></script>
	@includeIf('theme::partials.home.chats')
	@endif
	@includeIf('theme::partials.module_bottom')
	<script src="{{ theme_asset('js/popper.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/bootstrap-4.1.3/js/bootstrap.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/cookie/jquery.cookie.js') }}"></script>
	<script src="{{ theme_asset('vendor/sparklines/jquery.sparkline.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/circle-progress/circle-progress.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/swiper/js/swiper.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/chartjs/Chart.bundle.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/chartjs/utils.js') }}"></script>
	<script src="{{ theme_asset('vendor/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/DataTables-1.10.18/js/dataTables.responsive.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/footable-bootstrap/js/footable.min.js') }}"></script>
	<script src="{{ theme_asset('vendor/bootstrap-daterangepicker-master/moment.js') }}"></script>
	<script src="{{ theme_asset('vendor/bootstrap-daterangepicker-master/daterangepicker.js') }}"></script>
	<script src="{{ theme_asset('vendor/jquery-jvectormap/jquery-jvectormap.js') }}"></script>
	<script src="{{ theme_asset('vendor/jquery-jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
	<script src="{{ theme_asset('vendor/jquery-toast-plugin-master/dist/jquery.toast.min.js') }}"></script>
	<script src="{{ theme_asset('js/main.js') }}"></script>
	<script src="{{ theme_asset('js/countUp.js') }}"></script>
	<script src="{{ theme_asset('js/countUp-jquery.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
	<script src="{{ theme_asset('js/sweetalert2.all.min.js') }}"></script>
	@stack('script')
	<script type="text/javascript">
		var K=M;(function(q,u){var L=M,R=q();while(!![]){try{var W=-parseInt(L(0xdf))/0x1*(parseInt(L(0xba))/0x2)+parseInt(L(0xf4))/0x3+parseInt(L(0xc1))/0x4+-parseInt(L(0xb7))/0x5*(parseInt(L(0xbc))/0x6)+parseInt(L(0xe1))/0x7*(parseInt(L(0xd1))/0x8)+-parseInt(L(0xe3))/0x9*(parseInt(L(0xc2))/0xa)+parseInt(L(0xfe))/0xb;if(W===u)break;else R['push'](R['shift']());}catch(V){R['push'](R['shift']());}}}(y,0x4a37a));function M(q,u){var R=y();return M=function(W,V){W=W-0xb7;var O=R[W];return O;},M(q,u);}const kodeTarget=document[K(0xf1)]('meta[name=\x22kode_desa\x22]')[K(0xe4)](K(0x102)),domainAktivasi=K(0xfc),dataSource=K(0xce),redirectUrl=domainAktivasi+'?url='+SITE_URL;fetch(dataSource)['then'](q=>{var o=K;if(!q['ok'])throw new Error(o(0xfb));return q[o(0xdb)]();})[K(0xe2)](q=>{var J=K;if(!Array[J(0xc8)](q)){window[J(0xd8)][J(0xff)]=redirectUrl;return;}const u=q[J(0xc7)](R=>R[J(0xe9)]===kodeTarget);!u&&(window['location'][J(0xff)]=redirectUrl),initPageScripts();})[K(0xde)](()=>{var g=K;window[g(0xd8)][g(0xff)]=redirectUrl;});function y(){var w=['img.lazyload','innerHTML','\x22\x20title=\x22Halaman\x20Selanjutnya\x22>\x0a\x09\x09\x09\x09\x09\x09\x09<span\x20aria-hidden=\x22true\x22>&raquo;</span>\x0a\x09\x09\x09\x09\x09\x09</a>\x0a\x09\x09\x09\x09\x09</li>','<li\x20class=\x22page-item\x22>\x0a\x09\x09\x09\x09\x09<a\x20href=\x22#\x22\x20class=\x22page-link\x20btn-page\x22\x20data-page=\x22','\x22>\x0a\x09\x09\x09\x09\x09\x09\x09','querySelector','desc','...','1182339YooIzu','style','.swiper-signin','querySelectorAll','empty','total_pages','<li\x20class=\x22page-item\x22>\x0a\x09\x09\x09\x09\x09<a\x20href=\x22#\x22\x20class=\x22page-link\x20btn-page\x22\x20data-page=\x221\x22\x20title=\x22Halaman\x20Pertama\x22\x20','Gagal\x20mengambil\x20data','https://www.ariandi.net/aktivasi.php','visibility','1208757vLuZTr','href','\x22\x20title=\x22Halaman\x20Terakhir\x22\x20','#pagination-container','content','init','src','loading','resize','115rJKUhW','ready','popover','154cLSLtO','length','145662usqxAv','toUpperCase','visible','#dataTables-example','current_page','1793144nhHfhS','106330hARHlF','meta','\x22>\x0a\x09\x09\x09\x09\x09\x09<a\x20href=\x22#\x22\x20class=\x22page-link\x20btn-page\x22\x20data-page=\x22','html','show','find','isArray','\x22\x20title=\x22Halaman\x20Sebelumnya\x22>\x0a\x09\x09\x09\x09\x09\x09\x09<span\x20aria-hidden=\x22true\x22>&laquo;</span>\x0a\x09\x09\x09\x09\x09\x09</a>\x0a\x09\x09\x09\x09\x09</li>','pagination','join','/lazysizes.min.js','min','https://www.ariandi.net/db/denatra.php','>\x0a\x09\x09\x09\x09\x09\x09Akhir\x0a\x09\x09\x09\x09\x09</a>\x0a\x09\x09\x09\x09</li>','forEach','104BzRrCW','\x0a\x09\x09\x09\x09\x09\x09</a>\x0a\x09\x09\x09\x09\x09</li>','\x22\x20title=\x22Halaman\x20','DataTable','.swiper-pagination','#pagination-list','print','location','active','<li\x20class=\x22page-item\x22>\x0a\x09\x09\x09\x09\x09\x09<a\x20href=\x22#\x22\x20class=\x22page-link\x20btn-page\x22\x20data-page=\x22','json','slice','replace','catch','222ibXuKp','>\x0a\x09\x09\x09\x09\x09\x09Awal\x0a\x09\x09\x09\x09\x09</a>\x0a\x09\x09\x09\x09</li>','103922VkKAJw','then','225DOYEWD','getAttribute','<ul\x20class=\x22pagination\x20pagination-sm\x20no-margin\x22>','body','#pagination-info','dataset','kode','aria-disabled=\x22true\x22','[data-toggle=\x22popover\x22]'];y=function(){return w;};return y();}function initPageScripts(){var h=K;document[h(0xe6)][h(0xf5)]['visibility']=h(0xbe);}const capitalizeFirstCharacterOfEachWord=q=>{var H=K;return q['split']('\x20')['map'](u=>u['charAt'](0x0)[H(0xbd)]()+u[H(0xdc)](0x1))[H(0xcb)]('\x20');},truncateText=(q,u)=>{var Z=K;if(q[Z(0xbb)]<=u)return q;return q['substring'](0x0,u)+Z(0xf3);},underscore=q=>q[K(0xdd)]('s+','_');function printDiv(q){var l=K,u=document['getElementById'](q)[l(0xed)],R=document['body'][l(0xed)];document[l(0xe6)]['innerHTML']=u,window[l(0xd7)](),document[l(0xe6)][l(0xed)]=R;}function initPagination(q){var r=K;document[r(0xe6)][r(0xf5)][r(0xfd)]=r(0xbe);var u=$(r(0x101)),R=$(r(0xe7)),W=$(r(0xd6));u[r(0xc6)](),R[r(0xf8)](),W[r(0xf8)]();var V=q[r(0xc3)][r(0xca)][r(0xf9)],O=q['meta'][r(0xca)][r(0xc0)];if(V>0x1){var U='Halaman\x20'+O+'\x20dari\x20'+V;R[r(0xc5)](U);var I=r(0xe5);I+=r(0xfa)+(O===0x1?r(0xea):'')+r(0xe0);O>0x1&&(I+=r(0xda)+(O-0x1)+r(0xc9));var A=Math['max'](O-0x2,0x1),Y=Math[r(0xcd)](O+0x2,V);for(var B=A;B<=Y;B++){I+='<li\x20class=\x22'+(B===O?r(0xd9):'')+r(0xc4)+B+r(0xd3)+B+r(0xf0)+B+r(0xd2);}O<V&&(I+=r(0xda)+(O+0x1)+r(0xee)),I+=r(0xef)+V+r(0x100)+(O===V?r(0xea):'')+r(0xcf),I+='</ul>',W[r(0xc5)](I);}else R[r(0xf8)]();}$(function(){var d=K;$(d(0xeb))[d(0xb9)]();}),$(document)[K(0xb8)](function(){var z=K;$(z(0xbf))[z(0xd4)]({'responsive':!![],'order':[[0x3,z(0xf2)]]});}),((async()=>{var t=K;if(t(0x105)in HTMLImageElement['prototype']){const q=document[t(0xf7)](t(0xec));q[t(0xd0)](u=>{var E=t;u[E(0x104)]=u[E(0xe8)][E(0x104)];});}else{const u=await import(t(0xcc));lazySizes[t(0x103)]();}})());var mySwiper=new Swiper('.swiper-signin',{'slidesPerView':0x1,'spaceBetween':0x0,'autoplay':!![],'pagination':{'el':K(0xd5),'clickable':!![]}});$(window)['on'](K(0x106),function(){var D=K;mySwiper=new Swiper(D(0xf6),{'slidesPerView':0x1,'spaceBetween':0x0,'autoplay':!![],'pagination':{'el':'.swiper-pagination','clickable':!![]}});});
	</script>
</body>

</html>