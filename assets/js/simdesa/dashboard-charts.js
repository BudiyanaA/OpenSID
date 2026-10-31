/*
 * SIMDESA - grafik dashboard (Beranda)
 * Memakai Highcharts yang sudah ada di aplikasi (assets/js/highcharts).
 * Data berasal dari window.SD_DASHBOARD (App\Services\DashboardBeranda).
 * Warna diambil dari CSS token di assets/css/simdesa/tokens.css.
 */
(function () {
    'use strict';

    if (!window.Highcharts || !window.SD_DASHBOARD) {
        return;
    }

    var H = window.Highcharts;
    var data = window.SD_DASHBOARD;
    var css = getComputedStyle(document.documentElement);
    var token = function (nama, cadangan) {
        return (css.getPropertyValue(nama) || '').trim() || cadangan;
    };

    var warna = {
        blue: token('--simdesa-chart-blue', '#1565c0'),
        cyan: token('--simdesa-chart-cyan', '#22a6c9'),
        green: token('--simdesa-chart-green', '#2e9d6a'),
        purple: token('--simdesa-chart-purple', '#7c5cc4'),
        amber: token('--simdesa-chart-amber', '#f0a030'),
        soft: token('--simdesa-chart-soft', '#a9cdf5'),
        text: token('--simdesa-text', '#0f1f3d'),
        muted: token('--simdesa-text-muted', '#5b6b85'),
        grid: token('--simdesa-border', '#dbe4f0'),
        font: token('--simdesa-font-sans', 'sans-serif')
    };

    var angka = function (nilai) {
        return H.numberFormat(nilai, 0, ',', '.');
    };

    var rupiahSingkat = function (nilai) {
        var abs = Math.abs(nilai);
        if (abs >= 1e12) return 'Rp ' + H.numberFormat(nilai / 1e12, 1, ',', '.') + ' T';
        if (abs >= 1e9) return 'Rp ' + H.numberFormat(nilai / 1e9, 1, ',', '.') + ' M';
        if (abs >= 1e6) return 'Rp ' + H.numberFormat(nilai / 1e6, 1, ',', '.') + ' jt';
        return 'Rp ' + angka(nilai);
    };

    var gradasi = function (hex, atas, bawah) {
        return {
            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
            stops: [
                [0, H.color(hex).setOpacity(atas).get('rgba')],
                [1, H.color(hex).setOpacity(bawah).get('rgba')]
            ]
        };
    };

    H.setOptions({
        lang: { thousandsSep: '.', decimalPoint: ',', numericSymbols: null },
        colors: [warna.blue, warna.cyan, warna.green, warna.purple, warna.amber],
        chart: {
            backgroundColor: 'transparent',
            style: { fontFamily: warna.font },
            spacing: [8, 4, 4, 4],
            animation: { duration: 700 }
        },
        title: { text: null },
        credits: { enabled: false },
        exporting: { enabled: false },
        accessibility: { enabled: false },
        legend: {
            align: 'right',
            verticalAlign: 'top',
            symbolRadius: 6,
            symbolHeight: 10,
            symbolWidth: 10,
            itemStyle: { color: warna.muted, fontWeight: '500', fontSize: '12px' },
            itemHoverStyle: { color: warna.text }
        },
        tooltip: {
            backgroundColor: '#ffffff',
            borderWidth: 0,
            borderRadius: 10,
            shadow: { color: 'rgba(15,31,61,0.12)', offsetX: 0, offsetY: 6, width: 18, opacity: 1 },
            padding: 12,
            style: { color: warna.text, fontSize: '12px' },
            useHTML: true
        },
        xAxis: {
            lineColor: warna.grid,
            tickLength: 0,
            labels: { style: { color: warna.muted, fontSize: '11px' } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: H.color(warna.grid).setOpacity(0.6).get('rgba'),
            gridLineDashStyle: 'Dash',
            labels: { style: { color: warna.muted, fontSize: '11px' } }
        },
        plotOptions: {
            series: { animation: { duration: 800 } },
            column: { borderRadius: 6, borderWidth: 0 }
        }
    });

    // Catatan: admin-style.css mewarnai .highcharts-color-0/1 secara global
    // (halaman statistik), maka grafik di sini memakai colorIndex >= 10.
    var ada = function (id) {
        return document.getElementById(id);
    };

    // Komposisi jenis kelamin (donut)
    if (data.penduduk && ada('sd-chart-gender')) {
        H.chart('sd-chart-gender', {
            chart: { type: 'pie', spacing: [0, 0, 0, 0] },
            tooltip: {
                pointFormatter: function () {
                    return '<b>' + angka(this.y) + '</b> jiwa (' + H.numberFormat(this.percentage, 1, ',', '.') + '%)';
                }
            },
            plotOptions: {
                pie: {
                    innerSize: '74%',
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    borderRadius: 8,
                    dataLabels: { enabled: false },
                    states: { hover: { halo: { size: 6 } } }
                }
            },
            series: [{
                name: 'Penduduk',
                data: [
                    { name: 'Laki-laki', y: data.penduduk.laki, color: warna.blue, colorIndex: 10 },
                    { name: 'Perempuan', y: data.penduduk.perempuan, color: warna.cyan, colorIndex: 11 }
                ]
            }]
        });
    }

    // Rentang umur (kolom berkelompok)
    if (data.penduduk && data.penduduk.umur && ada('sd-chart-umur')) {
        var umur = data.penduduk.umur;
        H.chart('sd-chart-umur', {
            chart: { type: 'column', spacingTop: 18 },
            legend: { floating: true, y: -6, itemStyle: { fontSize: '11px' } },
            xAxis: {
                categories: umur.map(function (r) {
                    return r.nama.replace(/^UMUR\s+/i, '').replace(/\s*TAHUN$/i, '').replace(/\s*S\/D\s*/i, '–');
                }),
                // Sumbu cukup menampilkan batas bawah; rentang lengkap ada di tooltip
                labels: {
                    autoRotation: false,
                    style: { fontSize: '10px', textOverflow: 'none' },
                    formatter: function () { return String(this.value).split('–')[0]; }
                }
            },
            yAxis: { allowDecimals: false },
            tooltip: { shared: true, headerFormat: '<span style="font-weight:600">{point.key} tahun</span><br/>' },
            plotOptions: { column: { groupPadding: 0.12, pointPadding: 0.06, maxPointWidth: 22 } },
            series: [
                { name: 'Laki-laki', colorIndex: 10, color: gradasi(warna.blue, 1, 0.75), data: umur.map(function (r) { return r.laki; }) },
                { name: 'Perempuan', colorIndex: 11, color: gradasi(warna.cyan, 1, 0.75), data: umur.map(function (r) { return r.perempuan; }) }
            ]
        });
    }

    // Surat layanan per bulan (area halus)
    if (data.layanan && ada('sd-chart-layanan')) {
        H.chart('sd-chart-layanan', {
            chart: { type: 'areaspline' },
            legend: { enabled: false },
            xAxis: {
                categories: data.layanan.bulan.map(function (b) { return b.label; }),
                // Sumbu cukup nama bulan; bulan + tahun tampil di tooltip
                labels: { autoRotation: false, style: { fontSize: '10px', textOverflow: 'none' }, formatter: function () { return String(this.value).split(' ')[0]; } }
            },
            yAxis: { allowDecimals: false, min: 0 },
            tooltip: { headerFormat: '<span style="font-weight:600">{point.key}</span><br/>', pointFormat: '<b>{point.y}</b> surat' },
            plotOptions: {
                areaspline: {
                    lineWidth: 3,
                    fillColor: gradasi(warna.blue, 0.28, 0.02),
                    marker: { enabled: false, radius: 4, symbol: 'circle', lineWidth: 2, lineColor: '#ffffff' }
                }
            },
            series: [{ name: 'Surat', colorIndex: 10, color: warna.blue, data: data.layanan.bulan.map(function (b) { return b.jumlah; }) }]
        });
    }

    // Keuangan: anggaran vs realisasi (bar horizontal, ringkas)
    if (data.keuangan && data.keuangan.pelaksanaan && ada('sd-chart-keuangan')) {
        var pelaksanaan = data.keuangan.pelaksanaan;
        H.chart('sd-chart-keuangan', {
            chart: { type: 'bar', spacing: [4, 4, 0, 0] },
            legend: { align: 'right', verticalAlign: 'top', margin: 2, padding: 0, itemStyle: { fontSize: '11px' } },
            xAxis: { categories: pelaksanaan.map(function (p) { return p.judul; }), labels: { style: { fontSize: '11px' } } },
            yAxis: { tickAmount: 3, labels: { autoRotation: false, style: { fontSize: '10px' }, formatter: function () { return rupiahSingkat(this.value).replace('Rp ', ''); } } },
            tooltip: {
                shared: true,
                formatter: function () {
                    var html = '<span style="font-weight:600">' + this.x + '</span>';
                    this.points.forEach(function (p) {
                        html += '<br/><span style="color:' + (p.series.userOptions.titik || p.color) + '">●</span> ' + p.series.name + ': <b>Rp ' + angka(p.y) + '</b>';
                    });
                    return html;
                }
            },
            plotOptions: { bar: { borderRadius: 5, borderWidth: 0, groupPadding: 0.14, pointPadding: 0.04, maxPointWidth: 14 } },
            series: [
                { name: 'Anggaran', colorIndex: 12, titik: warna.soft, color: warna.soft, data: pelaksanaan.map(function (p) { return p.anggaran; }) },
                { name: 'Realisasi', colorIndex: 13, titik: warna.green, color: warna.green, data: pelaksanaan.map(function (p) { return p.realisasi; }) }
            ]
        });
    }

    // Sesuaikan ukuran grafik setelah tata letak grid stabil
    window.addEventListener('load', function () {
        H.charts.forEach(function (c) { if (c) c.reflow(); });
    });

    // Tab grafik Statistik Desa
    document.querySelectorAll('[data-sd-tab]').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            var grup = tombol.closest('.sd-panel');
            grup.querySelectorAll('[data-sd-tab]').forEach(function (t) {
                var aktif = t === tombol;
                t.classList.toggle('is-active', aktif);
                t.setAttribute('aria-selected', aktif ? 'true' : 'false');
                var pane = document.getElementById(t.getAttribute('data-sd-tab'));
                if (pane) pane.classList.toggle('is-active', aktif);
            });
            H.charts.forEach(function (c) { if (c) c.reflow(); });
        });
    });
})();
