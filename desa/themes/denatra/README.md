<h1 align="center">Selamat Datang di Tema DeNatra!</h1>

<p align="center">
  <img style="max-width: 100%;" width="500" alt="Tema DeNatra" src="https://user-images.githubusercontent.com/46939846/147866426-d50e1d2b-1ead-43de-b562-6f83fa85a1e1.png">
</p>

## Tentang Tema DeNatra
Tema DeNatra adalah salah satu tema yang digunakan untuk halaman website OpenSID (Sistem Informasi Desa).

Tema ini dibuat oleh Ariandi Ryan Kahfi, S.Pd. dengan mengangkat nama Desa Natai Raya yang disingkat menjadi DeNatra.

Sebelumnya ada Tema Natra (https://github.com/OpenSID/tema-natra) kependekan dari Natai Raya yang merupakan pemenang Sayembara Tema Web OpenSID 2019.

## Keterangan Tambahan
Berikut adalah langkah-langkah untuk melakukan perubahan pada Tema DeNatra:

### Sesuaikan Tampilan Widget di halaman utama website
Jika Anda ingin menyesuaikan tampilan widget di halaman utama bagian bawah, lakukan hal berikut:
- Edit file `denatra/resources/views/partials/module_home.blade.php` (baris 132, 135, 138).

(secara default ada 4 widget, statistik.php, peta_wilayah_desa.php, peta_lokasi_kantor.php dan komentar.php)

### Tambahkan Script pada Bagian Meta Web
Jika Anda ingin menambahkan script pada bagian meta web (sebelum tag `</head>`), ikuti langkah berikut:
- Sisipkan pada file `denatra/resources/views/partials/module_top.blade.php`.

### Tambahkan Script pada Bagian Footer Web
Untuk menambahkan script pada bagian footer web (sebelum tag `</body>`), lakukan hal berikut:
- Sisipkan pada file `denatra/resources/views/partials/module_bottom.blade.php`.

### Sesuaikan Sidebar
Jika Anda perlu menyesuaikan sidebar (tombol bagian atas), ikuti langkah ini:
- Edit file `denatra/resources/views/commons/sidebar.blade.php` sesuai kebutuhan.

### Sesuaikan Banner di Halaman Utama
Untuk mengubah link tujuan dan link gambar pada banner di halaman utama, lakukan hal berikut:
- Edit file `denatra/resources/views/partials/home/banner.blade.php` sesuai kebutuhan.

Pastikan untuk menyimpan perubahan yang Anda buat setelah mengedit file-file tersebut. Semoga panduan ini membantu!
