/*
 * SIMDESA - Smart Desa AI (generator artikel & pengaturan)
 * Tidak pernah menerima maupun mengirim token API: token hanya diproses server.
 */
(function ($) {
    'use strict';

    // Isi teks secara aman (tanpa HTML)
    function teks($el, nilai) {
        $el.text(nilai || '');
    }

    function tampilPesan($el, jenis, pesan) {
        $el.removeClass('sd-alert-danger sd-alert-success sd-alert-warning sd-alert-info')
            .addClass('sd-alert-' + jenis)
            .empty()
            .append($('<p>').text(pesan))
            .show();
    }

    function salin(teksSalin, $tombol) {
        var selesai = function () {
            var asli = $tombol.html();
            $tombol.html('<i class="fa fa-check"></i> Tersalin');
            setTimeout(function () { $tombol.html(asli); }, 1500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(teksSalin).then(selesai);
            return;
        }

        var $tmp = $('<textarea>').val(teksSalin).css({ position: 'fixed', opacity: 0 }).appendTo('body');
        $tmp[0].select();
        document.execCommand('copy');
        $tmp.remove();
        selesai();
    }

    function pesanAjaxGagal(xhr) {
        if (xhr && xhr.responseJSON && xhr.responseJSON.pesan) {
            return xhr.responseJSON.pesan;
        }
        if (xhr && xhr.status === 0) {
            return 'Koneksi ke server terputus. Periksa jaringan lalu coba lagi.';
        }
        return 'Terjadi kesalahan (' + (xhr ? xhr.status : '?') + '). Coba lagi.';
    }

    /* ------------------------------------------------------------ Generator */
    function initGenerator(cfg) {
        var state = { id: null, editor: null, editing: false, sibuk: false };
        var $form = $('#sd-ai-form');

        function setSibuk(sibuk) {
            state.sibuk = sibuk;
            $('#sd-ai-generate, #sd-ai-ulang, #sd-ai-draft, #sd-ai-publish').prop('disabled', sibuk);
            $('#sd-ai-generate span').text(sibuk ? 'Menyusun artikel…' : 'Generate Artikel');
        }

        function isiSekarang() {
            if (state.editor) {
                return state.editor.getContent();
            }
            return $('#sd-ai-pratinjau').html();
        }

        function perbaruiKuota(p) {
            if (!p) return;
            $('#sd-ai-kuota span').text(p.hari_ini + '/' + p.batas_harian);
            $('#sd-ai-kuota-user').text(p.user_jam + '/' + p.batas_user_jam);
        }

        function tampilkanHasil(a) {
            $('#hasil-judul').val(a.judul);
            // isi_html sudah dibersihkan server (tag teks saja, tanpa atribut)
            $('#sd-ai-pratinjau').html(a.isi_html);
            if (state.editor) {
                state.editor.setContent(a.isi_html);
            }
            teks($('#hasil-ringkasan'), a.ringkasan);
            teks($('#hasil-seo'), a.seo_description);
            teks($('#hasil-caption'), a.caption);
            teks($('#hasil-hashtag'), (a.hashtag || []).join(' '));

            state.id = null;
            $('#sd-ai-status-artikel').attr('class', 'sd-badge').text('Belum disimpan');
            $('#sd-ai-simpan-pesan').hide();

            $('#sd-ai-kosong, #sd-ai-memuat').hide();
            $('#sd-ai-hasil').show();
        }

        function generate() {
            if (state.sibuk) return;

            var formEl = $form[0];
            if (!formEl.checkValidity()) {
                formEl.reportValidity();
                return;
            }

            if (state.id === null && $('#sd-ai-hasil').is(':visible') && !confirm('Generate ulang akan mengganti hasil yang belum disimpan. Lanjutkan?')) {
                return;
            }

            $('#sd-ai-form-pesan').hide();
            setSibuk(true);
            $('#sd-ai-kosong, #sd-ai-hasil').hide();
            $('#sd-ai-memuat').show();

            $.ajax({
                url: cfg.urlGenerate,
                type: 'POST',
                dataType: 'json',
                data: $form.serialize(),
                timeout: 240000,
            }).done(function (res) {
                perbaruiKuota(res.pemakaian);
                if (res.sukses) {
                    tampilkanHasil(res.artikel);
                } else {
                    tampilPesan($('#sd-ai-form-pesan'), 'danger', res.pesan);
                    $('#sd-ai-memuat').hide();
                    $(state.id !== null || $('#hasil-judul').val() ? '#sd-ai-hasil' : '#sd-ai-kosong').show();
                }
            }).fail(function (xhr) {
                tampilPesan($('#sd-ai-form-pesan'), 'danger', pesanAjaxGagal(xhr));
                $('#sd-ai-memuat').hide();
                $($('#hasil-judul').val() ? '#sd-ai-hasil' : '#sd-ai-kosong').show();
            }).always(function () {
                setSibuk(false);
            });
        }

        function toggleEdit() {
            state.editing = !state.editing;
            $('#sd-ai-edit span').text(state.editing ? 'Selesai' : 'Edit');

            if (state.editing) {
                $('#hasil-isi').val($('#sd-ai-pratinjau').html());
                $('#sd-ai-pratinjau').hide();
                $('#sd-ai-editor-wrap').show();

                if (!state.editor && window.tinymce) {
                    tinymce.init({
                        selector: '#hasil-isi',
                        license_key: 'gpl',
                        height: 420,
                        menubar: false,
                        promotion: false,
                        branding: false,
                        plugins: 'lists link autolink wordcount',
                        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist blockquote | link | removeformat',
                        block_formats: 'Paragraf=p; Subjudul=h2; Subjudul kecil=h3',
                        setup: function (ed) { state.editor = ed; },
                    });
                } else if (state.editor) {
                    state.editor.setContent($('#sd-ai-pratinjau').html());
                }
            } else {
                if (state.editor) {
                    $('#sd-ai-pratinjau').html(state.editor.getContent());
                }
                $('#sd-ai-editor-wrap').hide();
                $('#sd-ai-pratinjau').show();
            }
        }

        function simpan(aksi) {
            if (state.sibuk) return;

            if (aksi === 'publish' && !confirm('Publikasikan artikel ini ke Website Desa?')) {
                return;
            }

            setSibuk(true);

            $.ajax({
                url: cfg.urlSimpan,
                type: 'POST',
                dataType: 'json',
                data: {
                    aksi: aksi,
                    id: state.id || '',
                    judul: $('#hasil-judul').val(),
                    isi: isiSekarang(),
                    id_kategori: $('#hasil-kategori').val(),
                },
            }).done(function (res) {
                var $pesan = $('#sd-ai-simpan-pesan');

                if (!res.sukses) {
                    tampilPesan($pesan, 'danger', res.pesan);
                    return;
                }

                state.id = res.id;
                $('#sd-ai-status-artikel')
                    .attr('class', 'sd-badge ' + (res.publish ? 'sd-badge-success' : 'sd-badge-warning'))
                    .text(res.publish ? 'Dipublikasikan' : 'Draft');

                tampilPesan($pesan, 'success', res.pesan);
                var $tautan = $('<p class="sd-ai-links">');
                $tautan.append($('<a target="_blank" rel="noopener">').attr('href', res.urlEditor).html('<i class="fa fa-pencil-square-o"></i> Buka di editor Artikel'));
                if (res.publish) {
                    $tautan.append($('<a target="_blank" rel="noopener">').attr('href', res.urlWebsite).html('<i class="fa fa-external-link"></i> Lihat di Website Desa'));
                }
                $pesan.append($tautan);
            }).fail(function (xhr) {
                tampilPesan($('#sd-ai-simpan-pesan'), 'danger', pesanAjaxGagal(xhr));
            }).always(function () {
                setSibuk(false);
            });
        }

        $('#sd-ai-generate, #sd-ai-ulang').on('click', generate);
        $('#sd-ai-edit').on('click', toggleEdit);
        $('#sd-ai-draft').on('click', function () { simpan('draft'); });
        $('#sd-ai-publish').on('click', function () { simpan('publish'); });
        $(document).on('click', '[data-salin]', function () {
            salin($($(this).data('salin')).text(), $(this));
        });

        // Peringatkan bila meninggalkan halaman dengan hasil yang belum disimpan
        window.addEventListener('beforeunload', function (e) {
            if ($('#sd-ai-hasil').is(':visible') && state.id === null) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    /* ------------------------------------------------------------ Pengaturan */
    function initPengaturan(cfg) {
        var $status = $('#sd-ai-status');

        $('#provider').on('change', function () {
            // Model milik penyedia lain tidak berlaku: kosongkan & isi model bawaan
            var bawaan = $(this).find(':selected').data('model-bawaan') || '';
            var $model = $('#model').empty();
            if (bawaan) {
                $model.append($('<option>').val(bawaan).text(bawaan));
            } else {
                $model.append($('<option>').val('').text('— simpan token lalu Test Koneksi untuk memuat model —'));
            }
            $('#sd-ai-uji').prop('disabled', true).attr('title', 'Simpan pengaturan terlebih dahulu');
        });

        $('#sd-ai-uji').on('click', function () {
            var $btn = $(this).prop('disabled', true);
            var $hasil = $('#sd-ai-uji-hasil');
            $btn.find('i').addClass('fa-spin');

            $.ajax({ url: cfg.urlUji, type: 'POST', dataType: 'json', timeout: 60000 })
                .done(function (res) {
                    tampilPesan($hasil, res.sukses ? 'success' : 'danger', res.pesan);
                    $status.attr('class', 'sd-ai-status sd-ai-status-' + (res.sukses ? 'success' : 'danger'))
                        .find('i').attr('class', 'fa ' + (res.sukses ? 'fa-check-circle' : 'fa-times-circle'));
                    $status.find('span').text(res.sukses ? 'Terhubung' : 'Gagal Terhubung');

                    if (res.sukses && res.models && res.models.length) {
                        var $model = $('#model');
                        var dipilih = $model.val() || res.model;
                        $model.empty();
                        res.models.forEach(function (id) {
                            $model.append($('<option>').val(id).text(id));
                        });
                        if (dipilih && res.models.indexOf(dipilih) === -1) {
                            $model.prepend($('<option>').val(dipilih).text(dipilih + ' (tidak ada di daftar)'));
                        }
                        $model.val(dipilih || res.models[0]);
                        tampilPesan($hasil, 'success', res.pesan + ' Pilih model lalu klik Simpan Pengaturan.');
                    }
                })
                .fail(function (xhr) {
                    tampilPesan($hasil, 'danger', pesanAjaxGagal(xhr));
                })
                .always(function () {
                    $btn.prop('disabled', false).find('i').removeClass('fa-spin');
                });
        });
    }

    $(function () {
        if (window.SD_AI) initGenerator(window.SD_AI);
        if (window.SD_AI_PENGATURAN) initPengaturan(window.SD_AI_PENGATURAN);
    });
})(jQuery);
