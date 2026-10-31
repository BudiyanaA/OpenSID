<aside class="control-sidebar control-sidebar-light">
    <div class="box-body">
        <div class="box-group" id="accordion">
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Aplikasi
                            <?= config_item('nama_aplikasi') ?></a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse in">
                    <div class="box-body">
                        <p align="justify"><?= config_item('nama_aplikasi') ?> adalah aplikasi Sistem Informasi Desa
                            (SID) yang dikembangkan dan disediakan untuk mendukung kebutuhan administrasi pemerintahan
                            desa. Informasi hak cipta dan lisensi dapat dilihat pada bagian Hak Cipta, Syarat, Dan
                            Ketentuan.</p>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#catatan-rilis">Catatan Rilis</a>
                    </h4>
                </div>
                <div id="catatan-rilis" class="panel-collapse collapse">
                    <div class="box-body">
                        <div class="catatan-scroll">
                            <?= parsedown('catatan_rilis.md') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Apakah SID ?</a>
                    </h4>
                </div>
                <div id="collapse2" class="panel-collapse collapse">
                    <div class="box-body">
                        <p align="justify">Aplikasi Sistem Informasi Desa (SID) adalah sistem olah data dan informasi
                            berbasis komputer yang dapat dikelola oleh pemerintah dan komunitas desa dalam dua ranah:
                        </p>
                        <dl>
                            <dt>1. Offline</dt>
                            <dd align="justify">Aplikasi diinstall dalam komputer server di kantor desa dan dioperasikan
                                sebagai server (pusat data) yang bersifat lokal. Karena tidak terhubung ke internet, SID
                                offline hanya bisa diakses dalam jaringan lokal. Sistem offline ini direkomendasikan
                                untuk diterapkan dalam penggunaan aplikasi SID harian. Database dari hasil proses olah
                                data secara offline itu dapat diunggah ke sistem online secara berkala.</dd>
                            <dt>2. Online</dt>
                            <dd align="justify">SID akan optimal jika terhubung ke internet sebagai sistem online
                                berbasis web. SID online akan otomatis berfungsi juga sebagai website desa. Website desa
                                ini memiliki fungsi yang terbagi dalam dua bagian, yakni bagian depan yang bisa diakses
                                oleh publik dan bagian dalam yang hanya bisa diakses oleh administrator sistem.</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Manajemen Akses SID</a>
                    </h4>
                </div>
                <div id="collapse3" class="panel-collapse collapse">
                    <div class="box-body">
                        <p align="justify">Aplikasi SID dirancang untuk mengelola data dasar desa dan informasi desa.
                            Data dasar yang dikelola meliputi data dasar kependudukan dan data dasar aset/sumber daya
                            desa. Data dasar ini menjadi tanggung jawab pemerintah desa dalam pengelolaannya. Hanya
                            pengguna (user) dari pemerintah desa dan tim yang dikoordinasikan oleh pemerintah desa saja
                            yang akan memiliki kewenangan dan hak akses ke dalam sistem. Sementara, user di luar
                            pemerintah desa hanya akan memiliki akses terbatas pada fungsi olah informasi untuk website
                            desa.</p>
                        <p align="justify">Tingkat user (pengguna) dalam SID:</p>
                        <p align="justify">
                        <ol>
                            <li align="justify">
                                Administrator : adalah orang/tim yang bertanggung jawab penuh atas olah data dan
                                informasi dalam SID dan website desa. Orang/tim ini ditunjuk oleh pemerintah desa
                                disahkan dengan surat keputusan kepala desa.
                                <ol>
                                    <li align="justify">Peran olah data : entry, edit, delete data dasar</li>
                                    <li align="justify">Peran olah informasi : tulis, edit, publish artikel website</li>
                                </ol>
                            </li>
                            <li align="justify">
                                Operator: adalah orang/tim yang bertugas membantu administrator mengelola data dan
                                informasi, tetapi dengan kewenangan yang lebih terbatas.
                                <ol>
                                    <li align="justify">Peran olah data : entry, edit data dasar</li>
                                    <li align="justify">Peran olah informasi : tulis, edit artikel website.</li>
                                </ol>
                            </li>
                            <li align="justify">
                                Redaksi: adalah orang/tim yang bertugas sebagai redaksi media website desa dan hanya
                                dapat melakukan olah informasi berupa artikel website.
                                <ol>
                                    <li align="justify">Peran olah informasi : tulis, edit artikel. Redaksi boleh
                                        mengubah semua artikel, termasuk menjadikan berita utama, aktif/non-aktifkan,
                                        masukkan ke slider, dsbnya</li>
                                </ol>
                            </li>
                            <li align="justify">
                                Kontributor: adalah orang/tim yang bertugas menulis artikel untuk disetujui redaksi
                                untuk ditampilkan di website desa.
                                <ol>
                                    <li align="justify">Peran olah informasi : tulis, edit artikel yang dibuatnya
                                        sendiri. Kontributor tidak dapat menjadikan artikel manapun menjadi berita utama,
                                        aktif/non-aktifkan atau memasukkan ke slider.</li>
                                </ol>
                            </li>
                        </ol>
                        </p>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Tahapan Membangun SID</a>
                    </h4>
                </div>
                <div id="collapse4" class="panel-collapse collapse">
                    <div class="box-body">
                        <P align="justify">Bagaimana memulai membangun Sistem Informasi Desa (SID) di desa kita? Caranya
                            sangat mudah, tetapi pasti perlu proses yang harus sabar dan cermat untuk dijalani. Siapa
                            pun Anda, baik perorangan maupun mewakili organisasi/lembaga, dapat mencoba mulai membangun
                            SID di desa masing-masing mengikuti langkah-langkah berikut.</P>
                        <ol>
                            <li align="justify">Bentuk tim kerja bersama pemerintah desa</li>
                            <li align="justify">Diskusikan basis data apa saja yang diperlukan untuk warga</li>
                            <li align="justify">Himpun data kependudukan warga dari Kartu Keluarga (KK)</li>
                            <li align="justify">Hubungi penyedia layanan <?= config_item('nama_aplikasi') ?> untuk
                                mendapatkan aplikasi SID</li>
                            <li align="justify">Pasang aplikasi SID di komputer desa</li>
                            <li align="justify">Masukkan data penduduk ke SID</li>
                            <li align="justify">Basis data kependudukan sudah bisa dimanfaatkan</li>
                            <li align="justify">Diskusikan rencana pengembangan SID sesuai kebutuhan desa</li>
                            <li align="justify">Sebarluaskan informasi desa melalui beragam media untuk warga</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Hak Cipta, Syarat, Dan
                            Ketentuan</a>
                    </h4>
                </div>
                <div id="collapse5" class="panel-collapse collapse">
                    <div class="box-body">
                        <p align="justify">Aplikasi Sistem Informasi Desa (SID) dibangun dan dikembangkan pada awalnya
                            oleh COMBINE Resource Institution sejak tahun 2009. Sistem ini dikelola dengan merujuk pada
                            lisensi GNU General Public License Version 3.</p>
                        <p align="justify"><?= config_item('nama_aplikasi') ?> dikembangkan berdasarkan OpenSID, aplikasi
                            sumber terbuka yang dikembangkan sejak Mei 2016 dan sejak Januari 2019 dikelola oleh
                            Perkumpulan Desa Digital Terbuka (OpenDesa) selaku pemegang hak cipta utama OpenSID.</p>
                        <p align="justify">Sesuai lisensi GPL v3, kode sumber <?= config_item('nama_aplikasi') ?>
                            sebagai ubahan dari OpenSID juga berlisensi GPL v3.</p>
                    </div>
                </div>
            </div>
            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">Kontak Dan Informasi</a>
                    </h4>
                </div>
                <div id="collapse6" class="panel-collapse collapse">
                    <div class="box-body">
                        <p align="justify">Untuk panduan penggunaan, bantuan teknis, pelaporan masalah, dan
                            pembaruan aplikasi, silakan hubungi penyedia layanan <?= config_item('nama_aplikasi') ?>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
