<script>
	$('#ChatSupport').czmChatSupport({
	button: {
		position: "left", /* left, right atau false. "position:false" untuk nonaktifkan */
		style: 1, /* Jenis tombol. Tulis antara nomor 1 sampai 7 */
		src: '<i class="fa fa-whatsapp"></i>',
		backgroundColor: "#10c379",
		effect: 7, /* Efek tombol. Tulis antara nomor 1 sampai 7 */
		notificationNumber: false,
		speechBubble: false,
		pulseEffect: true,
		text: {
			title: "{{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}",
			description: "{{ THEME_NAME }} {{ THEME_VERSION }}",
			online: "Selamat Datang.",
			offline: "Kami segera kembali."
		}
	},
	popup: {
		automaticOpen: false,
		outsideClickClosePopup: true,
		effect: 1, /* Efek pembuka icon. Tulis antara nomor 1 sampai 15 */
		header: {
			backgroundColor: "#10c379",
		},
		persons: [
		{
			avatar: {
				src: '<img src="{{ gambar_desa($desa['logo']) }}" alt="">',
				backgroundColor: "#ffffff",
				onlineCircle: false
			},
			text: {
				title: "{{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}",
				description: "Tema {{ THEME_NAME }} {{ THEME_VERSION }}",
				message: "Halo 🙂<br>Ada yang bisa kami bantu?",
				textbox: "Ketik di sini",
				button: false
			},
			link: {
				desktop: "https://api.whatsapp.com/send?phone={{ format_telpon($desa['nomor_operator']) }}&text={{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}",
				mobile: "https://wa.me/{{ format_telpon($desa['nomor_operator']) }}/?text={{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}"
			},
			onlineDay: {
				@php
					$mapHari = [
						'Minggu' => 'sunday',
						'Senin' => 'monday',
						'Selasa' => 'tuesday',
						'Rabu' => 'wednesday',
						'Kamis' => 'thursday',
						'Jumat' => 'friday',
						'Sabtu' => 'saturday',
					];
				@endphp

				@foreach ($jam_kerja as $value)
					@if ($value->status)
						{{ $mapHari[$value->nama_hari] }}: "{{ $value->jam_masuk }}-{{ $value->jam_keluar }}",
					@else
						{{ $mapHari[$value->nama_hari] }}: false,
					@endif
				@endforeach
			}
		},
		]
	},
	sound: false,
	changeBrowserTitle: "{{ setting('website_title') . ' ' . trim(ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa']) }}",
	cookie: false,
});
</script>