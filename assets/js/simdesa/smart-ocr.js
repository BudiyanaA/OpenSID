/*
 * SIMDESA - Smart OCR KTP & KK
 *
 * OCR berjalan sepenuhnya di browser (Tesseract.js + WebAssembly, di-host lokal
 * di assets/js/simdesa/ocr/tesseract). Gambar KTP/KK TIDAK pernah diunggah ke
 * server maupun layanan pihak ketiga. Hasil OCR hanya dipakai untuk MENGISI field
 * form yang sudah ada; penyimpanan tetap memakai tombol Simpan existing.
 *
 * Catatan privasi: file ini sengaja tidak memakai console.* agar teks hasil OCR,
 * NIK, maupun gambar tidak pernah muncul di log/console.
 */
(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    /* ------------------------------------------------------------------
     * Util teks
     * ------------------------------------------------------------------ */
    var DIGIT_FIX = { O: '0', o: '0', D: '0', Q: '0', U: '0', I: '1', l: '1', i: '1', '|': '1', '!': '1', L: '1', T: '7', Z: '2', z: '2', S: '5', s: '5', B: '8', G: '6', b: '6', g: '9', q: '9', A: '4', '?': '7' };

    function norm(text) {
        return String(text || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
    }

    function clean(text) {
        return String(text || '')
            .replace(/[“”"`'‘’]/g, '')
            .replace(/^[\s:;.,\-_=~|]+/, '')
            .replace(/[\s:;,\-_=~|]+$/, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function cleanName(text) {
        return clean(String(text || '').replace(/[^A-Za-z .,'\-]/g, ' ')).toUpperCase();
    }

    // Ubah potongan teks yang "seharusnya angka" (hasil OCR sering tertukar O/0, I/1, dst).
    function toDigits(text) {
        return String(text || '').replace(/[A-Za-z|!?]/g, function (ch) {
            return DIGIT_FIX[ch] !== undefined ? DIGIT_FIX[ch] : ch;
        });
    }

    // Cari deret 16 digit (NIK / No. KK) di dalam teks.
    // Token digabung bila NIK terpotong spasi ("3204 0101 ..."); hanya token yang
    // mayoritas angka yang dipakai agar kata di sekitarnya tidak ikut terbaca.
    function find16(text) {
        text = String(text || '');
        var re = /\S+/g;
        var toks = [];
        var m;
        while ((m = re.exec(text)) !== null) {
            var s = m[0];
            var cut = s.match(/^[^\d]*[:;]/); // "NIK:3204..." -> "3204..."
            var off = cut ? cut[0].length : 0;
            toks.push({ s: s.slice(off).replace(/[.,;:]+$/, ''), i: m.index + off, end: m.index + s.length });
        }
        for (var a = 0; a < toks.length; a++) {
            var joined = '';
            for (var b = a; b < toks.length && b < a + 6; b++) {
                var t = toks[b].s;
                var digits = (t.match(/\d/g) || []).length;
                if (!t || digits < t.length * 0.6) {
                    break;
                }
                joined += toDigits(t).replace(/[^0-9]/g, '');
                if (joined.length === 16) {
                    return { value: joined, index: toks[a].i, length: toks[b].end - toks[a].i };
                }
                if (joined.length > 16) {
                    break;
                }
            }
        }

        return null;
    }

    function levenshtein(a, b) {
        if (a === b) {
            return 0;
        }
        if (!a.length) {
            return b.length;
        }
        if (!b.length) {
            return a.length;
        }
        var prev = [];
        var j;
        for (j = 0; j <= b.length; j++) {
            prev[j] = j;
        }
        for (var i = 1; i <= a.length; i++) {
            var cur = [i];
            for (j = 1; j <= b.length; j++) {
                cur[j] = Math.min(prev[j] + 1, cur[j - 1] + 1, prev[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1));
            }
            prev = cur;
        }

        return prev[b.length];
    }

    function similarity(a, b) {
        a = norm(a);
        b = norm(b);
        if (!a || !b) {
            return 0;
        }
        if (a === b) {
            return 1;
        }
        var score = 1 - levenshtein(a, b) / Math.max(a.length, b.length);
        // "PNS" di dalam "PEGAWAI NEGERI SIPIL (PNS)", dsb.
        if (a.length >= 3 && b.indexOf(a) !== -1) {
            score = Math.max(score, 0.7 + 0.3 * (a.length / b.length));
        }

        return score;
    }

    function bestMatch(text, list, minScore) {
        var best = null;
        var top  = 0;
        for (var i = 0; i < list.length; i++) {
            var s = similarity(text, list[i]);
            if (s > top) {
                top  = s;
                best = list[i];
            }
        }

        return top >= (minScore || 0.72) ? best : null;
    }

    function parseDate(text) {
        var m = toDigits(text).match(/(\d{1,2})\s*[-\/.,]\s*(\d{1,2})\s*[-\/.,]\s*(\d{4})/);
        if (!m) {
            return null;
        }
        var d = parseInt(m[1], 10);
        var mo = parseInt(m[2], 10);
        var y = parseInt(m[3], 10);
        if (d < 1 || d > 31 || mo < 1 || mo > 12 || y < 1900 || y > new Date().getFullYear()) {
            return null;
        }

        return { value: pad(d) + '-' + pad(mo) + '-' + y, raw: m[0], index: m.index };
    }

    function pad(n) {
        return (n < 10 ? '0' : '') + n;
    }

    // Tanggal lahir & jenis kelamin yang tersandi di NIK (digit 7-12: DDMMYY, tanggal +40 = perempuan).
    function infoDariNik(nik) {
        if (!/^\d{16}$/.test(nik || '')) {
            return {};
        }
        var d  = parseInt(nik.substr(6, 2), 10);
        var mo = parseInt(nik.substr(8, 2), 10);
        var yy = parseInt(nik.substr(10, 2), 10);
        var sex = d > 40 ? 'PEREMPUAN' : 'LAKI-LAKI';
        if (d > 40) {
            d -= 40;
        }
        var now  = new Date().getFullYear() % 100;
        var year = (yy > now ? 1900 : 2000) + yy;
        var valid = d >= 1 && d <= 31 && mo >= 1 && mo <= 12;

        return { sex: sex, tanggal: valid ? pad(d) + '-' + pad(mo) + '-' + year : null };
    }

    /* ------------------------------------------------------------------
     * Ekstraksi "Label : Nilai" (bisa lebih dari satu label per baris, mis. header KK)
     * ------------------------------------------------------------------ */
    function extractLabels(lines, defs, firstCell) {
        var out = {};
        lines.forEach(function (line, lineIndex) {
            var hits = [];
            defs.forEach(function (def) {
                var re = new RegExp(def.re.source, 'gi');
                var m;
                while ((m = re.exec(line)) !== null) {
                    hits.push({ key: def.key, start: m.index, end: m.index + m[0].length });
                    if (m[0].length === 0) {
                        re.lastIndex++;
                    }
                }
            });
            hits.sort(function (a, b) {
                return a.start - b.start || (b.end - b.start) - (a.end - a.start);
            });
            // buang label yang tumpang tindih
            var used = [];
            hits.forEach(function (h) {
                var last = used[used.length - 1];
                if (!last || h.start >= last.end) {
                    used.push(h);
                }
            });
            used.forEach(function (h, i) {
                var next  = used[i + 1];
                var value = line.slice(h.end, next ? next.start : line.length);
                if (firstCell) {
                    // KTP: teks di kolom kanan (tempat/tgl terbit, foto) terpisah spasi lebar
                    value = value.replace(/^[\s:;.]+/, '').split(/\s{4,}/)[0];
                }
                value = clean(value);
                if (value && !out[h.key]) {
                    out[h.key] = value;
                    out['_line_' + h.key] = lineIndex;
                }
            });
        });

        return out;
    }

    var AGAMA = ['ISLAM', 'KRISTEN', 'KATHOLIK', 'KATOLIK', 'HINDU', 'BUDHA', 'BUDDHA', 'KHONGHUCU', 'KONGHUCU', 'KEPERCAYAAN'];
    var KAWIN = ['BELUM KAWIN', 'KAWIN', 'CERAI HIDUP', 'CERAI MATI', 'KAWIN TERCATAT', 'KAWIN BELUM TERCATAT'];
    var SHDK = ['KEPALA KELUARGA', 'SUAMI', 'ISTRI', 'ANAK', 'MENANTU', 'CUCU', 'ORANGTUA', 'ORANG TUA', 'MERTUA', 'FAMILI LAIN', 'PEMBANTU', 'LAINNYA'];
    var PENDIDIKAN = ['TIDAK/BELUM SEKOLAH', 'BELUM TAMAT SD/SEDERAJAT', 'TAMAT SD/SEDERAJAT', 'SLTP/SEDERAJAT', 'SLTA/SEDERAJAT', 'DIPLOMA I/II', 'AKADEMI/DIPLOMA III/S. MUDA', 'DIPLOMA IV/STRATA I', 'STRATA II', 'STRATA III'];

    function parseSex(text) {
        var t = norm(text);
        if (/PEREM|EREMPU|WANITA/.test(t)) {
            return 'PEREMPUAN';
        }
        if (/LAKI|AKILA|PRIA/.test(t)) {
            return 'LAKI-LAKI';
        }

        return null;
    }

    function parseGoldar(text) {
        if (/TIDAK\s*TAHU/i.test(text || '')) {
            return 'TIDAK TAHU';
        }
        var m = String(text || '').toUpperCase().replace(/0/g, 'O').match(/\b(AB|A|B|O)\s*([+-])?(?![A-Z])/);

        return m ? m[1] + (m[2] || '') : null;
    }

    function parseKawin(text) {
        var t = norm(text);
        if (!t) {
            return null;
        }
        if (/BELUM/.test(t) && !/TERCATAT/.test(t)) {
            return 'BELUM KAWIN';
        }
        if (/CERAIH|HIDUP/.test(t)) {
            return 'CERAI HIDUP';
        }
        if (/CERAIM|MATI/.test(t)) {
            return 'CERAI MATI';
        }
        if (/KAWIN|KAWlN|TERCATAT/.test(t)) {
            return 'KAWIN';
        }

        return bestMatch(text, KAWIN.slice(0, 4), 0.6);
    }

    function parseWn(text) {
        var t = norm(text);
        if (/WNA/.test(t)) {
            return 'WNA';
        }
        if (/WN[I1L]|INDONESIA/.test(t)) {
            return 'WNI';
        }

        return null;
    }

    /* ------------------------------------------------------------------
     * Parser KTP
     *
     * Label KTP sering rusak saat di-OCR ("mpauTgi Lahu", "gama"), sedangkan
     * nilainya terbaca baik. Karena urutan baris KTP selalu sama, setiap baris
     * dicocokkan ke urutan label (dynamic programming) memakai kemiripan label
     * DAN pola nilainya (tanggal, 16 digit, LAKI-LAKI/PEREMPUAN, dst).
     * ------------------------------------------------------------------ */
    var PROVINSI_KODE = [11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 31, 32, 33, 34, 35, 36, 51, 52, 53, 61, 62, 63, 64, 65, 71, 72, 73, 74, 75, 76, 81, 82, 91, 92, 93, 94, 95, 96, 97];

    // Kode provinsi (2 digit awal NIK). Wilayah Papua dilewati karena kodenya berubah sejak pemekaran 2022.
    var PROVINSI_NAMA = {
        11: 'ACEH', 12: 'SUMATERA UTARA', 13: 'SUMATERA BARAT', 14: 'RIAU', 15: 'JAMBI', 16: 'SUMATERA SELATAN', 17: 'BENGKULU', 18: 'LAMPUNG',
        19: 'KEPULAUAN BANGKA BELITUNG', 21: 'KEPULAUAN RIAU', 31: 'DKI JAKARTA', 32: 'JAWA BARAT', 33: 'JAWA TENGAH', 34: 'DAERAH ISTIMEWA YOGYAKARTA',
        35: 'JAWA TIMUR', 36: 'BANTEN', 51: 'BALI', 52: 'NUSA TENGGARA BARAT', 53: 'NUSA TENGGARA TIMUR', 61: 'KALIMANTAN BARAT', 62: 'KALIMANTAN TENGAH',
        63: 'KALIMANTAN SELATAN', 64: 'KALIMANTAN TIMUR', 65: 'KALIMANTAN UTARA', 71: 'SULAWESI UTARA', 72: 'SULAWESI TENGAH', 73: 'SULAWESI SELATAN',
        74: 'SULAWESI TENGGARA', 75: 'GORONTALO', 76: 'SULAWESI BARAT', 81: 'MALUKU', 82: 'MALUKU UTARA',
    };

    // Kode provinsi dari judul KTP ("PROVINSI SULAWESI SELATAN"), null bila tidak yakin.
    function kodeProvinsi(nama) {
        var n = norm(nama);
        if (!n) {
            return null;
        }
        if (/JAKARTA/.test(n)) {
            return 31;
        }
        if (/YOGYA/.test(n)) {
            return 34;
        }
        var best = null;
        var top = 0;
        Object.keys(PROVINSI_NAMA).forEach(function (k) {
            var sc = similarity(n, PROVINSI_NAMA[k]);
            if (sc > top) {
                top = sc;
                best = parseInt(k, 10);
            }
        });

        return top >= 0.85 ? best : null;
    }

    var KTP_SEQ = [
        { key: 'nik', label: 'NIK', bonus: function (v) {
            return (toDigits(v).match(/\d/g) || []).length >= 10 ? 0.6 : 0;
        } },
        { key: 'nama', label: 'NAMA', bonus: function (v) {
            return /[A-Za-z]{3,}/.test(v) && !/\d{3}/.test(v) ? 0.1 : 0;
        } },
        { key: 'ttl', label: 'TEMPAT/TGL LAHIR', bonus: function (v) {
            return findDate(v) ? 0.6 : (/\d{2}\D{0,2}\d{2}\D{0,2}\d{2,4}/.test(v) ? 0.3 : 0);
        } },
        { key: 'sex', label: 'JENIS KELAMIN', bonus: function (v) {
            return parseSex(v) ? 0.6 : 0;
        } },
        { key: 'alamat', label: 'ALAMAT' },
        { key: 'rtrw', label: 'RT/RW', bonus: function (v) {
            return parseRtRw(v) ? 0.5 : 0;
        } },
        { key: 'desa', label: 'KEL/DESA' },
        { key: 'kecamatan', label: 'KECAMATAN' },
        { key: 'agama', label: 'AGAMA', bonus: function (v) {
            return bestMatch(v.split(/\s+/)[0] || '', AGAMA, 0.7) ? 0.6 : 0;
        } },
        { key: 'kawin', label: 'STATUS PERKAWINAN', bonus: function (v) {
            return /KAW[I1l]N|CERA[I1l]/i.test(v) ? 0.6 : 0;
        } },
        { key: 'pekerjaan', label: 'PEKERJAAN' },
        { key: 'wn', label: 'KEWARGANEGARAAN', bonus: function (v) {
            return parseWn(v) ? 0.4 : 0;
        } },
        { key: 'berlaku', label: 'BERLAKU HINGGA', bonus: function (v) {
            return /SEUMUR|HIDUP/i.test(v) || findDate(v) ? 0.4 : 0;
        } },
    ];

    // Tanggal kalender yang benar-benar valid (bukan sekadar pola angka).
    function validDate(d, m, y) {
        if (m < 1 || m > 12 || d < 1 || y < 1900 || y > new Date().getFullYear()) {
            return false;
        }

        return d <= new Date(y, m, 0).getDate();
    }

    // Cari tanggal DD-MM-YYYY. Koreksi huruf->angka hanya dilakukan di potongan
    // yang memang berbentuk tanggal, dan hasilnya wajib tanggal yang valid.
    function findDate(text) {
        var s = String(text || '');
        var re = /([0-9OoDIl|!SBGZ]{1,2})\s*[-\/.,~_ ]\s*([0-9OoDIl|!SBGZ]{1,2})\s*[-\/.,~_ ]\s*([0-9OoDIl|!SBGZ]{4})(?![0-9])/g;
        var m;
        while ((m = re.exec(s)) !== null) {
            var digits = (m[0].match(/\d/g) || []).length;
            if (digits < 6) {
                continue;
            }
            var d = parseInt(toDigits(m[1]), 10);
            var mo = parseInt(toDigits(m[2]), 10);
            var y = parseInt(toDigits(m[3]), 10);
            if (validDate(d, mo, y)) {
                return { value: pad(d) + '-' + pad(mo) + '-' + y, index: m.index, raw: m[0] };
            }
        }
        // "07081985" tanpa pemisah
        var m8 = s.match(/(?:^|\D)(\d{2})(\d{2})((?:19|20)\d{2})(?!\d)/);
        if (m8 && validDate(+m8[1], +m8[2], +m8[3])) {
            return { value: m8[1] + '-' + m8[2] + '-' + m8[3], index: s.indexOf(m8[1] + m8[2] + m8[3]), raw: m8[1] + m8[2] + m8[3] };
        }

        return null;
    }

    function parseRtRw(text) {
        var m = String(text || '').match(/([0-9OoDIl]{1,3})\s*[\/|\\1lI!]\s*([0-9OoDIl]{1,3})(?![0-9])/);
        if (!m) {
            return null;
        }
        var rt = toDigits(m[1]);
        var rw = toDigits(m[2]);

        return /^\d+$/.test(rt) && /^\d+$/.test(rw) ? { rt: rt, rw: rw } : null;
    }

    // Struktur NIK: kode provinsi, kab/kota, kecamatan, tanggal lahir (+40 perempuan), bulan, nomor urut.
    function validNik(nik) {
        if (!/^\d{16}$/.test(nik || '')) {
            return false;
        }
        var prov = parseInt(nik.substr(0, 2), 10);
        var day = parseInt(nik.substr(6, 2), 10);
        var mon = parseInt(nik.substr(8, 2), 10);
        if (PROVINSI_KODE.indexOf(prov) === -1 || nik.substr(2, 2) === '00' || nik.substr(4, 2) === '00' || nik.substr(12) === '0000') {
            return false;
        }
        if (mon < 1 || mon > 12) {
            return false;
        }

        return (day >= 1 && day <= 31) || (day >= 41 && day <= 71);
    }

    // Apakah tanggal lahir (DD-MM-YYYY) konsisten dengan NIK.
    function nikCocokTanggal(nik, tanggal) {
        if (!validNik(nik) || !tanggal) {
            return false;
        }
        var p = tanggal.split('-');
        var d = parseInt(nik.substr(6, 2), 10);

        return (d > 40 ? d - 40 : d) === parseInt(p[0], 10) && nik.substr(8, 2) === p[1] && nik.substr(10, 2) === p[2].substr(2);
    }

    // Kandidat NIK dari satu teks. whitelistDigits=true: teks hasil OCR khusus angka.
    function nikCandidates(text, whitelistDigits) {
        var out = [];
        var push = function (v) {
            if (validNik(v) && out.indexOf(v) === -1) {
                out.push(v);
            }
        };
        String(text || '').split(/\n/).forEach(function (line) {
            var digits;
            if (whitelistDigits) {
                digits = line.replace(/\D/g, '');
            } else {
                // koreksi huruf->angka hanya pada token yang mayoritas angka
                digits = line.split(/\s+/).filter(function (t) {
                    var n = (t.match(/\d/g) || []).length;

                    return n >= 2 && n >= t.replace(/[.,:;]/g, '').length * 0.6;
                }).map(function (t) {
                    return toDigits(t).replace(/\D/g, '');
                }).join('');
            }
            if (digits.length < 16 || digits.length > 19) {
                return;
            }
            // 16 digit utuh, atau buang 1-3 digit derau di tepi (":" terbaca "1", dsb.)
            for (var i = 0; i + 16 <= digits.length; i++) {
                push(digits.substr(i, 16));
            }
        });

        return out;
    }

    function splitLabelValue(text) {
        var t = String(text || '');
        var m = t.match(/^(.{0,40}?)\s*[:;“”"=]\s*(.*)$/);
        if (m && m[1].replace(/[^A-Za-z]/g, '').length >= 2) {
            return { label: m[1], value: m[2] };
        }
        var g = t.match(/^(\S.*?)\s{3,}(.*)$/);
        if (g && g[1].length <= 30) {
            return { label: g[1], value: g[2] };
        }

        return { label: '', value: t };
    }

    // Skor kecocokan satu baris dengan satu label urutan KTP.
    function lineScore(line, def) {
        var best = { score: -1, value: line.value };
        var coba = function (label, value) {
            var sim = similarity(label, def.label);
            var s = sim + (def.bonus ? def.bonus(value) : 0) - 0.45;
            if (sim < 0.3 && !(def.bonus && def.bonus(value) >= 0.5)) {
                s = -1;
            }
            if (s > best.score) {
                best = { score: s, value: value };
            }
        };
        coba(line.label, line.value);
        // label tanpa titik dua: coba 1-3 kata pertama sebagai label
        var words = line.text.trim().split(/\s+/);
        for (var k = 1; k <= Math.min(3, words.length - 1); k++) {
            coba(words.slice(0, k).join(' '), words.slice(k).join(' '));
        }

        return best;
    }

    function alignKtp(lines) {
        var n = lines.length;
        var m = KTP_SEQ.length;
        var dp = [];
        var from = [];
        var i;
        var j;
        for (i = 0; i <= n; i++) {
            dp.push(new Array(m + 1).fill(0));
            from.push(new Array(m + 1).fill(null));
        }
        var cache = {};
        for (i = 1; i <= n; i++) {
            for (j = 1; j <= m; j++) {
                var opts = [[dp[i - 1][j], 'skipLine'], [dp[i][j - 1], 'skipLabel']];
                var sc = lineScore(lines[i - 1], KTP_SEQ[j - 1]);
                cache[i + ':' + j] = sc;
                if (sc.score > 0) {
                    opts.push([dp[i - 1][j - 1] + sc.score, 'match']);
                }
                opts.sort(function (a, b) {
                    return b[0] - a[0];
                });
                dp[i][j] = opts[0][0];
                from[i][j] = opts[0][1];
            }
        }
        var hasil = {};
        i = n;
        j = m;
        while (i > 0 && j > 0) {
            var f = from[i][j];
            if (f === 'match') {
                var c = cache[i + ':' + j];
                hasil[KTP_SEQ[j - 1].key] = { value: c.value, line: i - 1, score: c.score };
                i--;
                j--;
            } else if (f === 'skipLine') {
                i--;
            } else {
                j--;
            }
        }
        hasil._score = dp[n][m];

        return hasil;
    }

    function toLine(text) {
        var t = String(text || '').replace(/\s+$/, '');
        var lv = splitLabelValue(t);
        // kolom kanan KTP (tempat/tgl terbit, foto) terpisah spasi lebar
        lv.value = clean(lv.value.replace(/^[\s:;.]+/, '').split(/\s{4,}/)[0]);

        return { text: t, label: lv.label, value: lv.value };
    }

    function ttlParts(value) {
        var v = String(value || '');
        var date = findDate(v);
        var tempat = date ? v.slice(0, date.index) : v.split(/[,.]/)[0];
        // buang sisa label ("TGL LAHIR", "LAHIR") yang ikut terbaca
        var words = cleanName(tempat.split(',')[0]).split(' ').filter(Boolean);
        while (words.length && ['TEMPAT', 'TGL', 'LAHIR', 'TANGGAL'].some(function (w) {
            return similarity(words[0], w) >= 0.7;
        })) {
            words.shift();
        }
        tempat = words.join(' ');

        return { tempat: tempat.replace(/[^A-Z ]/g, '').trim().length >= 3 ? tempat : '', tanggal: date ? date.value : '' };
    }

    /**
     * Parse teks KTP (hasil satu kali OCR). `lines` boleh berupa teks atau
     * array baris {text}. Hasil: nilai per field + kandidat NIK/TTL mentah.
     */
    function parseKtpLines(lines) {
        if (!Array.isArray(lines)) {
            lines = String(lines || '').split(/\r?\n/);
        }
        var L = lines.map(function (l) {
            return typeof l === 'string' ? toLine(l) : Object.assign(toLine(l.text), { src: l });
        }).filter(function (l) {
            return clean(l.text) !== '';
        });
        var a = alignKtp(L);
        var v = function (k) {
            return a[k] ? a[k].value : '';
        };
        var all = L.map(function (l) {
            return l.text;
        }).join('\n');
        var res = { _align: a, _lines: L };

        L.forEach(function (l) {
            var m;
            if (!res.provinsi && (m = l.text.match(/PROV[I1l]NS[I1l]\s*[:;.]?\s*(.+)/i))) {
                res.provinsi = cleanName(m[1]);
            } else if (!res.kabupaten && (m = l.text.match(/^\s*(KABUPATEN|KOTA|KAB\.)\s+(.+)/i))) {
                res.kabupaten = cleanName(m[0]);
            }
        });

        res.nikKandidat = nikCandidates(v('nik'), false);
        if (!res.nikKandidat.length) {
            res.nikKandidat = nikCandidates(all, false);
        }
        res.nama = cleanName(v('nama'));
        var ttl = ttlParts(v('ttl'));
        res.tempatlahir = ttl.tempat;
        res.tanggallahir = ttl.tanggal;
        var sexLine = a.sex ? L[a.sex.line].text : '';
        res.sex = parseSex(v('sex')) || '';
        var gd = (sexLine || all).match(/Da\s*r\s*a\s*\w?\s*[:;.]?\s*(.{0,4})/i);
        res.goldar = gd ? (parseGoldar(gd[1]) || '') : '';
        res.alamat = clean(v('alamat')).toUpperCase();
        var rr = parseRtRw(v('rtrw'));
        if (rr) {
            res.rt = rr.rt;
            res.rw = rr.rw;
        }
        res.desa = cleanName(v('desa'));
        res.kecamatan = cleanName(v('kecamatan'));
        res.agama = bestMatch(v('agama').split(/\s+/)[0] || '', AGAMA, 0.6) || '';
        res.kawin = parseKawin(v('kawin')) || '';
        res.pekerjaan = clean(v('pekerjaan')).toUpperCase();
        res.wn = parseWn(v('wn')) || (/(^|[^A-Z])WN[I1l|](?![A-Z])/.test(all) ? 'WNI' : '');

        return res;
    }

    // Sejajarkan bacaan s ke referensi 16 digit (global alignment / edit distance).
    // map[i] = indeks digit s untuk posisi i referensi, atau -1 bila digit itu hilang di s.
    function alignDigits(ref, s) {
        var n = ref.length;
        var m = s.length;
        var D = [];
        var i;
        var j;
        for (i = 0; i <= n; i++) {
            D.push(new Array(m + 1));
            D[i][0] = i;
        }
        for (j = 0; j <= m; j++) {
            D[0][j] = j;
        }
        for (i = 1; i <= n; i++) {
            for (j = 1; j <= m; j++) {
                D[i][j] = Math.min(D[i - 1][j - 1] + (ref[i - 1] === s[j - 1] ? 0 : 1), D[i - 1][j] + 1, D[i][j - 1] + 1);
            }
        }
        var map = new Array(n);
        for (i = n, j = m; i > 0 || j > 0;) {
            if (i > 0 && j > 0 && D[i][j] === D[i - 1][j - 1] + (ref[i - 1] === s[j - 1] ? 0 : 1)) {
                map[--i] = --j;
            } else if (i > 0 && D[i][j] === D[i - 1][j] + 1) {
                map[--i] = -1;
            } else {
                j--;
            }
        }

        return { map: map, dist: D[n][m] };
    }

    /**
     * Ensemble NIK per posisi digit dari SEMUA bacaan (varian preprocessing x PSM).
     * reads: [{d:'digit', conf:[0-100]|null, w:bobot}]; bacaan 14-20 digit disejajarkan ke 16 posisi
     * sehingga bacaan yang kelebihan/kekurangan satu digit tetap ikut bersuara di posisi lainnya.
     * Suara = bobot sumber x confidence digit Tesseract; bacaan dari gambar yang sama (kelompok k) berbagi
     * satu suara. Tanggal lahir & kode provinsi yang tercetak di KTP yang sama ikut bersuara pada posisinya.
     * Tidak ada tebakan dari luar KTP (database, dll).
     */
    function nikEnsemble(reads, opsi) {
        opsi = opsi || {};
        reads = reads.filter(function (r) {
            return r.d && r.d.length >= 14 && r.d.length <= 20;
        });
        var ukuran = {};
        reads.forEach(function (r) {
            ukuran[r.k] = (ukuran[r.k] || 0) + 1;
        });
        reads.forEach(function (r) {
            r.bw = r.w / ukuran[r.k];
        });
        var exact = reads.filter(function (r) {
            return r.d.length === 16;
        });
        if (!exact.length) {
            return null;
        }
        var luar = []; // suara dari teks lain di KTP: [posisi, digit, bobot]
        if (opsi.provKode) {
            var pk = pad(opsi.provKode);
            luar.push([0, pk[0], 1], [1, pk[1], 1]);
        }
        (opsi.tanggal || []).forEach(function (t) {
            var p = String(t.v).split('-');
            if (p.length !== 3 || p[2].length !== 4) {
                return;
            }
            // indeks 7: satuan hari (sama untuk laki-laki & perempuan +40), 8-9 bulan, 10-11 tahun
            var s = p[0].substr(1) + p[1] + p[2].substr(2);
            for (var k = 0; k < s.length; k++) {
                luar.push([7 + k, s[k], Math.min(1, t.w / 2)]); // tanggal yang terbaca sekali: setengah suara
            }
        });
        var hitung = function (ref, pakaiLuar) {
            var votes = [];
            var grup = [];
            var i;
            for (i = 0; i < 16; i++) {
                votes.push({});
                grup.push({});
            }
            var dipakai = 0;
            reads.forEach(function (r) {
                var al = r.d.length === 16 ? { map: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15], dist: 0 } : alignDigits(ref, r.d);
                if (r.d.length !== 16 && al.dist > 4) {
                    return;
                }
                dipakai++;
                for (i = 0; i < 16; i++) {
                    var j = al.map[i];
                    if (j < 0) {
                        continue;
                    }
                    var c = r.conf ? Math.max(0.2, Math.min(1, r.conf[j] / 100)) : 0.7;
                    votes[i][r.d[j]] = (votes[i][r.d[j]] || 0) + r.bw * c;
                    grup[i][r.d[j]] = grup[i][r.d[j]] || {};
                    grup[i][r.d[j]][r.k] = 1;
                }
            });
            if (pakaiLuar) {
                // bobot suara teks lain ~ seperempat total suara OCR di posisi itu (minimal 1)
                var totalPos = votes.map(function (v) {
                    return Object.keys(v).reduce(function (t, d) {
                        return t + v[d];
                    }, 0);
                });
                luar.forEach(function (v) {
                    votes[v[0]][v[1]] = (votes[v[0]][v[1]] || 0) + v[2] * Math.max(1, totalPos[v[0]] * 0.25);
                });
            }

            return { votes: votes, grup: grup, dipakai: dipakai };
        };
        var urutkan = function (obj) {
            return Object.keys(obj).sort(function (a, b) {
                return obj[b] - obj[a];
            });
        };
        // referensi awal: voting bacaan yang tepat 16 digit saja
        var ref = '';
        for (var i0 = 0; i0 < 16; i0++) {
            var ex = {};
            exact.forEach(function (r) {
                var c = r.conf ? Math.max(0.2, r.conf[i0] / 100) : 0.7;
                ex[r.d[i0]] = (ex[r.d[i0]] || 0) + r.bw * c;
            });
            ref += urutkan(ex)[0];
        }
        var res;
        for (var iter = 0; iter < 3; iter++) {
            res = hitung(ref, true);
            var baru = res.votes.map(function (v) {
                return urutkan(v)[0];
            }).join('');
            if (baru === ref) {
                break;
            }
            ref = baru;
        }
        var hOcr = hitung(ref, false);
        var ocrSaja = hOcr.votes;
        var posisi = res.votes.map(function (v, i) {
            var k = urutkan(v);
            var total = k.reduce(function (t, d) {
                return t + v[d];
            }, 0);

            return {
                d: k[0], share: total ? v[k[0]] / total : 0, d2: k[1] || null, share2: k[1] && total ? v[k[1]] / total : 0, grup: Object.keys(hOcr.grup[i][k[0]] || {}).length,
                ocr: ocrSaja[i][k[0]] || 0, ocrShare: (ocrSaja[i][k[0]] || 0) / (Object.keys(ocrSaja[i]).reduce(function (t, d) {
                    return t + ocrSaja[i][d];
                }, 0) || 1),
            };
        });
        var nik = posisi.map(function (p) {
            return p.d;
        }).join('');
        var tglCocok = (opsi.tanggal || []).some(function (t) {
            return nikCocokTanggal(nik, t.v);
        });
        var identikGrup = Object.keys(reads.reduce(function (o, r) {
            if (r.d === nik) {
                o[r.k] = 1;
            }

            return o;
        }, {})).length;
        var provCocok = !!opsi.provKode && parseInt(nik.substr(0, 2), 10) === opsi.provKode;
        var adaTgl = (opsi.tanggal || []).length > 0;
        var terkonfirmasi = tglCocok && (provCocok || !opsi.provKode);
        // Posisi 7-12 (tanggal lahir) yang cocok dengan tanggal lahir tercetak di KTP sudah dikonfirmasi
        // bacaan baris lain: cukup menang suara terbanyak & didukung minimal satu kelompok bacaan NIK.
        var cek = posisi.filter(function (p, i) {
            return !(tglCocok && i >= 6 && i <= 11 && p.grup >= 1);
        });
        // tanpa konfirmasi tanggal/provinsi, kesepakatan dinilai dari suara OCR saja
        var minShare = Math.min.apply(null, cek.map(function (p) {
            return terkonfirmasi ? p.share : p.ocrShare;
        }));
        var minGrup = Math.min.apply(null, cek.map(function (p) {
            return p.grup;
        }));
        var alasan = [];
        if (!validNik(nik)) {
            alasan.push('struktur NIK tidak valid');
        }
        if (opsi.provKode && !provCocok) {
            alasan.push('kode provinsi berbeda dengan judul KTP');
        }
        // Syarat diisi otomatis: tiap digit disepakati >=2 kelompok bacaan independen dengan mayoritas jelas.
        // Bila tanggal lahir & provinsi di KTP tidak mengonfirmasi, juga harus ada >=2 bacaan 16 digit identik
        // dari kelompok berbeda, dan bila tanggal terbaca tapi tidak cocok, mayoritas harus lebih kuat.
        if (adaTgl && !tglCocok && minShare < 0.8) {
            alasan.push('tidak cocok dengan tanggal lahir');
        } else if (minShare < 0.7) {
            alasan.push('ada digit yang tidak disepakati mayoritas');
        }
        if (minGrup < 2 || (!terkonfirmasi && identikGrup < 2)) {
            alasan.push('dukungan bacaan kurang');
        }
        // kandidat alternatif untuk operator: ganti digit yang paling ragu dengan pesaingnya
        var kandidat = [nik];
        posisi.map(function (p, i) {
            return { i: i, p: p };
        }).filter(function (x) {
            return x.p.d2 && x.p.share < 0.85;
        }).sort(function (a, b) {
            return a.p.share - b.p.share;
        }).forEach(function (x) {
            var alt = nik.substr(0, x.i) + x.p.d2 + nik.substr(x.i + 1);
            if (validNik(alt) && kandidat.indexOf(alt) === -1) {
                kandidat.push(alt);
            }
        });

        return {
            nik: nik, yakin: !alasan.length, alasan: alasan, cocokTgl: tglCocok, dipakai: res.dipakai, identikGrup: identikGrup, minShare: Math.round(minShare * 100) / 100, minGrup: minGrup, posisi: posisi, kandidat: kandidat.filter(validNik).slice(0, 3),
        };
    }

    /**
     * Pemilihan NIK berdasarkan KONSENSUS hasil OCR NIK (tanpa tanggal lahir, field lain, atau database).
     * Setiap hasil OCR (grayscale, threshold, sharpening, berbagai PSM, crop NIK, halaman penuh, ...)
     * dinormalisasi menjadi angka; bacaan tepat 16 digit = 1 suara penuh untuk angka tersebut.
     * Bacaan 17-19 digit (derau di tepi) memberi suara setengah, dibagi rata ke potongan 16 digitnya.
     * Kandidat utama = frekuensi tertinggi; confidence digit & konsistensi per posisi hanya pemecah seri.
     * Diisi otomatis bila kandidat utama muncul >= 2 kali dan dominan (>= 2x kandidat kedua);
     * bacaan lain yang berbeda tidak menggugurkan kandidat yang jelas berulang.
     */
    function nikPilih(sumber) {
        var kand = {};
        var bacaan16 = [];
        var totalBacaan = 0;
        var tambah = function (n, suara, penuh, conf, metode) {
            var c = kand[n] = kand[n] || { nik: n, suara: 0, jumlah: 0, metode: {}, conf: [] };
            c.suara += suara;
            c.jumlah += penuh ? 1 : 0;
            c.metode[metode] = 1;
            if (conf !== null) {
                c.conf.push(conf);
            }
        };
        sumber.forEach(function (s) {
            if (!s.nik || !s.nik.length) {
                return;
            }
            var metode = s.nama || s.grup || 'halaman';
            var baca = s.baca || (s.mentah || []).map(function (m) {
                return { d: m, conf: null };
            });
            if (!baca.length) {
                // halaman penuh: tidak ada bacaan mentah, suara dibagi rata antar kandidatnya
                totalBacaan++;
                s.nik.forEach(function (n) {
                    tambah(n, 1 / s.nik.length, s.nik.length === 1, null, metode);
                });

                return;
            }
            baca.forEach(function (b) {
                var d = String(b.d || '').replace(/\D/g, '');
                var potong = s.nik.filter(function (n) {
                    return d.indexOf(n) !== -1;
                });
                if (!potong.length) {
                    return;
                }
                totalBacaan++;
                if (d.length === 16) {
                    bacaan16.push(d);
                    var conf = b.conf && b.conf.length === 16 ? b.conf.reduce(function (t, x) {
                        return t + x;
                    }, 0) / 1600 : null;
                    tambah(d, 1, true, conf, metode);
                } else {
                    potong.forEach(function (n) {
                        tambah(n, 0.5 / potong.length, false, null, metode);
                    });
                }
            });
        });
        var list = Object.keys(kand).map(function (n) {
            var c = kand[n];
            var konsisten = 0;
            if (bacaan16.length) {
                bacaan16.forEach(function (b) {
                    for (var i = 0; i < 16; i++) {
                        konsisten += b[i] === n[i] ? 1 : 0;
                    }
                });
                konsisten /= bacaan16.length * 16;
            }
            c.suara = Math.round(c.suara * 100) / 100;
            c.jumlahMetode = Object.keys(c.metode).length;
            c.konsisten = Math.round(konsisten * 100) / 100;
            c.confRata = c.conf.length ? Math.round(c.conf.reduce(function (t, x) {
                return t + x;
            }, 0) / c.conf.length * 100) : null;

            return c;
        }).sort(function (a, b) {
            return b.suara - a.suara || b.jumlah - a.jumlah || b.konsisten - a.konsisten || (b.confRata || 0) - (a.confRata || 0);
        });
        var top = list[0];
        var kedua = list[1] ? list[1].suara : 0;
        var yakin = !!top && top.jumlah >= 2 && top.suara - kedua >= 1 && top.suara >= kedua * 2;

        return {
            nik: top ? top.nik : '',
            yakin: yakin,
            kandidat: list.slice(0, 5).map(function (c) {
                return c.nik;
            }),
            catatan: top ? 'terbaca identik ' + top.jumlah + ' dari ' + totalBacaan + ' hasil OCR' + (top.confRata !== null ? ', confidence ' + top.confRata + '%' : '') + ', mohon cocokkan dengan KTP' : '',
            info: list.slice(0, 5).map(function (c) {
                return { nik: c.nik, suara: c.suara, identik: c.jumlah, metode: c.jumlahMetode, conf: c.confRata, konsisten: c.konsisten };
            }),
        };
    }

    /**
     * Gabungkan kandidat dari beberapa sumber OCR menjadi keputusan akhir.
     * sumber: [{nik:[..], tanggal:'', tempat:'', bobot:1}]
     * Tidak mengarang: NIK hanya diisi bila struktur valid DAN (≥2 sumber sepakat
     * atau cocok dengan tanggal lahir). Selain itu hanya ditampilkan sebagai kandidat.
     */
    function decideKtp(sumber, opsi) {
        opsi = opsi || {};
        // Suara per bacaan: setiap bacaan OCR yang menghasilkan kandidat tepat 16 digit = 1 suara
        // (gambar berspasi & voting per posisi hanya 0.5 karena lebih lemah/turunan).
        var BOBOT = { 'nik-spasi': 0.5, konsensus: 0.5 };
        var skor = {};
        sumber.forEach(function (s) {
            var bobot = s.bobot || BOBOT[s.grup] || 1;
            (s.nik || []).forEach(function (n) {
                skor[n] = (skor[n] || 0) + bobot / Math.max(1, s.nik.length);
            });
        });
        var tanggalSkor = {};
        var tanggalJumlah = {};
        var tempatSkor = {};
        sumber.forEach(function (s) {
            if (s.tanggal) {
                tanggalSkor[s.tanggal] = (tanggalSkor[s.tanggal] || 0) + (s.bobot || 1);
                tanggalJumlah[s.tanggal] = (tanggalJumlah[s.tanggal] || 0) + 1;
            }
            if (s.tempat) {
                var k = norm(s.tempat);
                tempatSkor[k] = tempatSkor[k] || { v: s.tempat, s: 0 };
                tempatSkor[k].s += (s.bobot || 1);
            }
        });
        var urut = function (obj) {
            return Object.keys(obj).sort(function (a, b) {
                return obj[b] - obj[a];
            });
        };
        var nikList = urut(skor);
        var tglList = urut(tanggalSkor);
        var out = { nik: '', nikHint: '', tanggallahir: '', tempatlahir: '', catatan: {}, keyakinan: {} };

        // Keyakinan tanggal lahir dinilai sendiri: yakin hanya bila >= 2 bacaan independen
        // (halaman penuh / crop TTL varian A / B) menghasilkan tanggal yang sama dan TIDAK ada
        // bacaan lain yang berbeda. Hanya informasi, tidak mengubah nilai tanggal lahir.
        var tglYakin = tglList.length === 1 && tanggalJumlah[tglList[0]] >= 2;
        // Sementara tidak ada validasi silang antar field: tanggal lahir tidak dipakai untuk NIK.
        var tglValid = [];
        out.keyakinan.tanggallahir = tglYakin ? 'tinggi' : (tglList.length ? 'rendah' : 'tidak terbaca');

        // NIK: skor = bobot pembacaan + kecocokan tanggal lahir + kecocokan kode provinsi judul KTP.
        // Diisi hanya bila unggul jelas dari kandidat lain; kalau bertentangan -> tidak yakin.
        var provKode = kodeProvinsi(opsi.provinsi);
        var bacaan16 = [];
        sumber.forEach(function (s) {
            (s.mentah || []).forEach(function (m) {
                if (/^\d{16}$/.test(m)) {
                    bacaan16.push(m);
                }
            });
        });
        var adaProvCocok = provKode && nikList.some(function (n) {
            return parseInt(n.substr(0, 2), 10) === provKode;
        });
        var info = {};
        nikList.forEach(function (n) {
            var cocokTgl = tglValid.some(function (t) {
                return nikCocokTanggal(n, t);
            });
            var prov = provKode ? (parseInt(n.substr(0, 2), 10) === provKode ? 1 : -1) : 0;
            // konsistensi: rata-rata kesesuaian per posisi digit dengan semua bacaan 16 digit
            var cocokPosisi = 0;
            if (bacaan16.length > 1) {
                bacaan16.forEach(function (b) {
                    for (var i = 0; i < 16; i++) {
                        cocokPosisi += b[i] === n[i] ? 1 : 0;
                    }
                });
                cocokPosisi /= bacaan16.length * 16;
            }
            var total = skor[n] + (cocokTgl ? 2 : 0) + cocokPosisi * 2 + prov * 1.5;
            if (prov === -1 && adaProvCocok) {
                total -= 5; // ada kandidat lain yang cocok dengan provinsi di judul KTP
            }
            info[n] = { baca: Math.round(skor[n] * 100) / 100, cocokTgl: cocokTgl, prov: prov, posisi: Math.round(cocokPosisi * 100) / 100, total: Math.round(total * 100) / 100 };
        });
        nikList.sort(function (x, y) {
            return info[y].total - info[x].total;
        });
        out.nikInfo = info;
        // Ensemble per posisi digit (hanya informasi debug, tidak menentukan NIK).
        // k = kelompok bacaan: bacaan dari gambar yang sama (crop varian A, halaman penuh) berbagi satu suara.
        var BOBOT_BACA = { 'nik-khusus': 1, 'nik-spasi': 0.6 };
        var reads = [];
        sumber.forEach(function (s) {
            if (s.baca) {
                s.baca.forEach(function (b) {
                    reads.push({ d: b.d, conf: b.conf, w: BOBOT_BACA[s.grup] || 1, k: s.nama });
                });
            } else if (s.mentah) {
                s.mentah.forEach(function (m) {
                    reads.push({ d: m, conf: null, w: 1, k: s.kelompok || s.nama });
                });
            } else {
                (s.nik || []).forEach(function (n) {
                    reads.push({ d: n, conf: null, w: 1, k: s.grup || s.nama });
                });
            }
        });
        var ens = !sumber.some(function (s) {
            return s.baca;
        }) ? null : nikEnsemble(reads, {
            provKode: provKode, tanggal: tglValid.map(function (t) {
                return { v: t, w: tanggalSkor[t] };
            }),
        });
        out.ensemble = ens;

        // Keputusan NIK: hanya dari bacaan OCR NIK itu sendiri (nikPilih). Tidak memakai tanggal lahir,
        // provinsi, database, atau field lain. Bila kandidat teratas tidak unggul jelas -> tidak diisi.
        var pilih = nikPilih(sumber);
        out.nikPilih = pilih.info;
        if (pilih.yakin) {
            out.nik = pilih.nik;
            out.nikHints = [];
            out.catatan.nik = pilih.catatan;
        } else if (pilih.kandidat.length) {
            out.nikHints = pilih.kandidat;
            out.nikHint = pilih.kandidat[0];
        }
        out.keyakinan.nik = out.nik ? 'tinggi' : (pilih.kandidat.length ? 'rendah' : 'tidak terbaca');

        // Tanggal lahir: diproses independen dari hasil baca TTL saja (tidak memakai NIK).
        if (tglList.length && (tanggalSkor[tglList[0]] >= 1.6 || tglList.length === 1)) {
            out.tanggallahir = tglList[0];
            if (tanggalSkor[tglList[0]] < 1.6) {
                out.catatan.tanggallahir = 'terbaca satu kali, mohon diperiksa';
            }
        }

        // Tempat lahir
        var tempatKeys = Object.keys(tempatSkor).sort(function (a, b) {
            return tempatSkor[b].s - tempatSkor[a].s;
        });
        if (tempatKeys.length) {
            out.tempatlahir = tempatSkor[tempatKeys[0]].v;
            out.keyakinan.tempatlahir = tempatKeys.length === 1 && tempatSkor[tempatKeys[0]].s >= 2 ? 'tinggi' : 'rendah';
        }

        return out;
    }

    // Voting nama dari beberapa pembacaan; bila hanya beda spasi, pilih yang berspasi.
    function pilihNama(list) {
        var grup = {};
        list.filter(function (n) {
            return n && norm(n).length >= 2;
        }).forEach(function (n) {
            var k = norm(n);
            grup[k] = grup[k] || [];
            grup[k].push(n);
        });
        var keys = Object.keys(grup).sort(function (x, y) {
            return grup[y].length - grup[x].length;
        });
        if (!keys.length) {
            return list[0] || '';
        }

        return grup[keys[0]].sort(function (x, y) {
            return y.split(' ').length - x.split(' ').length;
        })[0];
    }

    // Kompatibel: parse dari satu teks OCR saja.
    function parseKtp(text) {
        var r = parseKtpLines(text);
        var dec = decideKtp([{ nik: r.nikKandidat, tanggal: r.tanggallahir, tempat: r.tempatlahir, grup: 'halaman' }], { provinsi: r.provinsi });
        r.nik = dec.nik;
        r.nikHint = dec.nikHint;
        r.tanggallahir = dec.tanggallahir;
        r.tempatlahir = dec.tempatlahir;
        r.catatan = dec.catatan;
        delete r._lines;
        delete r._align;

        return r;
    }

    /* ------------------------------------------------------------------
     * Parser KK
     * ------------------------------------------------------------------ */
    var KK_LABELS = [
        { key: 'kepala', re: /Nama\s*Kepala\s*Kel\w*/ },
        { key: 'alamat', re: /\bAlamat\b(?=\s*[:;])/ },
        { key: 'rtrw', re: /\bRT\s*[\/|l1I]?\s*RW\b/ },
        { key: 'desa', re: /(Desa\s*[\/|l1I]?\s*Kel\w*|Kel\w*\s*[\/|l1I]?\s*Desa|\bDesa\b(?=\s*[:;])|\bKelurahan\b(?=\s*[:;]))/ },
        { key: 'kecamatan', re: /Keca\w*tan/ },
        { key: 'kabupaten', re: /(Kabupaten\s*[\/|l1I]?\s*Kota|Kab\w*\s*[\/|l1I]\s*Kota|\bKabupaten\b(?=\s*[:;])|\bKota\b(?=\s*[:;]))/ },
        { key: 'kodepos', re: /Kode\s*Pos/ },
        { key: 'provinsi', re: /Prov[i1l]ns[i1l]/ },
    ];

    function splitCells(text) {
        return String(text || '').split(/\s{2,}|\s*[|]\s*/).map(clean).filter(function (c) {
            return c !== '';
        });
    }

    function rowNumber(line) {
        var m = toDigits(line.slice(0, 4)).match(/^\s*(\d{1,2})[.)]?\s/);

        return m ? parseInt(m[1], 10) : null;
    }

    function parseMemberRow(line, nik) {
        var row = { nik: nik.value, no: rowNumber(line) };
        var before = line.slice(0, nik.index).replace(/^\s*[0-9OoIl|]{1,2}[.)]?\s+/, '');
        row.nama = cleanName(before);

        var rest = line.slice(nik.index + nik.length);
        var sexMatch = rest.match(/(LAKI\s*[-~]?\s*LAKI|PEREMPUAN|PRIA|WANITA|L\s*A\s*K\s*I\S*|PEREM\S*)/i);
        if (sexMatch) {
            row.sex = parseSex(sexMatch[0]);
            rest = rest.slice(sexMatch.index + sexMatch[0].length);
        }

        var tgl = parseDate(rest);
        if (tgl) {
            row.tanggallahir = tgl.value;
            row.tempatlahir  = cleanName(rest.slice(0, tgl.index));
            rest = rest.slice(tgl.index + tgl.raw.length);
        }

        var cells = splitCells(rest);
        // gol. darah di kolom terakhir (format KK baru)
        if (cells.length && /^(AB|A|B|O|0)\s*[+-]?$|TIDAK\s*TAHU/i.test(cells[cells.length - 1])) {
            row.goldar = parseGoldar(cells.pop()) || '';
        }
        var words = cells.join(' ').split(/\s+/).filter(Boolean);

        // agama = kata pertama yang cocok
        for (var i = 0; i < Math.min(words.length, 3); i++) {
            var ag = bestMatch(words[i], AGAMA, 0.7);
            if (ag) {
                row.agama = ag;
                words = words.slice(i + 1);
                break;
            }
        }

        // pendidikan = prefix kata terbaik, sisanya pekerjaan
        var best = { score: 0, k: 0, value: null };
        for (var k = 1; k <= Math.min(words.length, 6); k++) {
            var prefix = words.slice(0, k).join(' ');
            PENDIDIKAN.forEach(function (p) {
                var s = similarity(prefix, p);
                if (s > best.score) {
                    best = { score: s, k: k, value: p };
                }
            });
        }
        if (best.score >= 0.7) {
            row.pendidikan = best.value;
            words = words.slice(best.k);
        }
        row.pekerjaan = clean(words.join(' ')).toUpperCase();

        return row;
    }

    function parseStatusRow(line) {
        var up = line.toUpperCase();
        var hub = up.match(/(KEPALA\s*KELUARGA|SUAMI|ISTRI|ANAK|MENANTU|CUCU|ORANG\s*TUA|MERTUA|FAMILI\s*LAIN|PEMBANTU|LAINNYA)/);
        var kaw = up.match(/(BELUM\s*KAWIN|CERAI\s*HIDUP|CERAI\s*MATI|KAWIN(\s*BELUM)?(\s*TERCATAT)?)/);
        if (!hub && !kaw) {
            return null;
        }
        var row = { no: rowNumber(line) };
        row.kawin    = kaw ? parseKawin(kaw[0]) : '';
        row.hubungan = hub ? hub[1].replace(/\s+/g, ' ').replace('ORANG TUA', 'ORANGTUA') : '';
        var wn = up.match(/\b(WN[I1L]|WNA)\b/);
        row.wn = wn ? parseWn(wn[0]) : '';

        var anchor = wn ? wn.index + wn[0].length : (hub ? hub.index + hub[0].length : 0);
        var cells = splitCells(line.slice(anchor)).filter(function (c) {
            return !/^[-–—.]+$/.test(c) && !/\d{3,}/.test(c);
        }).map(cleanName).filter(Boolean);
        if (cells.length >= 2) {
            row.ayah = cells[cells.length - 2];
            row.ibu  = cells[cells.length - 1];
        }

        return row;
    }

    function parseKk(text) {
        var lines = String(text || '').split(/\r?\n/).filter(function (l) {
            return clean(l) !== '';
        });
        var f   = extractLabels(lines, KK_LABELS);
        var res = {
            no_kk: '',
            kepala: f.kepala ? cleanName(f.kepala) : '',
            alamat: f.alamat ? clean(f.alamat).toUpperCase() : '',
            desa: f.desa ? cleanName(f.desa) : '',
            kecamatan: f.kecamatan ? cleanName(f.kecamatan) : '',
            kabupaten: f.kabupaten ? cleanName(f.kabupaten) : '',
            provinsi: f.provinsi ? cleanName(f.provinsi) : '',
            kodepos: f.kodepos ? (toDigits(f.kodepos).match(/\d{5}/) || [''])[0] : '',
            anggota: [],
        };
        if (f.rtrw) {
            var rr = toDigits(f.rtrw).match(/(\d{1,3})\s*[\/|\\ ]\s*(\d{1,3})/);
            if (rr) {
                res.rt = rr[1];
                res.rw = rr[2];
            }
        }

        // Nomor KK: deret 16 digit pertama sebelum tabel anggota
        var tabelMulai = lines.length;
        for (var i = 0; i < lines.length; i++) {
            if (/\bNIK\b/i.test(lines[i]) && /Nama/i.test(lines[i])) {
                tabelMulai = i;
                break;
            }
        }
        for (i = 0; i < Math.min(tabelMulai, lines.length); i++) {
            var cand = find16(lines[i]);
            if (cand) {
                res.no_kk = cand.value;
                break;
            }
        }

        var members  = [];
        var statuses = [];
        lines.forEach(function (line, idx) {
            var nik = find16(line);
            if (nik && !(idx < tabelMulai && nik.value === res.no_kk) && nik.value !== res.no_kk) {
                members.push(parseMemberRow(line, nik));

                return;
            }
            if (idx > tabelMulai || tabelMulai === lines.length) {
                var st = parseStatusRow(line);
                if (st && (st.no !== null || (st.hubungan && st.kawin))) {
                    statuses.push(st);
                }
            }
        });

        members.forEach(function (m, index) {
            var st = null;
            if (m.no !== null) {
                st = statuses.filter(function (s) {
                    return s.no === m.no;
                })[0];
            }
            st = st || statuses[index] || {};
            ['kawin', 'hubungan', 'wn', 'ayah', 'ibu'].forEach(function (key) {
                m[key] = st[key] || m[key] || '';
            });
            var dariNik = infoDariNik(m.nik);
            if (!m.sex && dariNik.sex) {
                m.sex = dariNik.sex;
            }
            if (!m.tanggallahir && dariNik.tanggal) {
                m.tanggallahir = dariNik.tanggal;
            }
        });
        res.anggota = members;

        return res;
    }

    /* ------------------------------------------------------------------
     * Mesin OCR (Tesseract.js lokal)
     * ------------------------------------------------------------------ */
    var engine = { worker: null, progress: null };

    function absUrl(path) {
        return new URL(path, window.location.href).href;
    }

    function getWorker(base) {
        if (!engine.worker) {
            if (!window.Tesseract || typeof WebAssembly !== 'object' || typeof Worker === 'undefined') {
                return Promise.reject(new Error('Browser tidak mendukung OCR (WebAssembly). Gunakan Chrome/Firefox/Edge versi terbaru.'));
            }
            engine.worker = window.Tesseract.createWorker('ind', window.Tesseract.OEM.LSTM_ONLY, {
                workerPath: absUrl(base + 'worker.min.js'),
                corePath: absUrl(base + 'core/'),
                langPath: absUrl(base + 'lang'),
                gzip: true,
                logger: function (m) {
                    if (engine.progress) {
                        engine.progress(m);
                    }
                },
                errorHandler: function () {},
            }).catch(function (e) {
                engine.worker = null;
                throw e;
            });
        }

        return engine.worker;
    }

    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                resolve({ img: img, url: url });
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('File bukan gambar yang valid.'));
            };
            img.src = url;
        });
    }

    // Skala, putar, ambil kanal terang (menghilangkan latar biru KTP / cap berwarna), perkuat kontras.
    function prepareCanvas(img, rotation, targetWidth) {
        var rot = ((rotation % 360) + 360) % 360;
        var w0 = img.naturalWidth;
        var h0 = img.naturalHeight;
        var sideways = rot === 90 || rot === 270;
        var w = sideways ? h0 : w0;
        var scale = targetWidth / w;
        var maxPixels = 12e6;
        if (w0 * h0 * scale * scale > maxPixels) {
            scale = Math.sqrt(maxPixels / (w0 * h0));
        }
        var cw = Math.round((sideways ? h0 : w0) * scale);
        var ch = Math.round((sideways ? w0 : h0) * scale);

        var canvas = document.createElement('canvas');
        canvas.width = cw;
        canvas.height = ch;
        var ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, cw, ch);
        ctx.translate(cw / 2, ch / 2);
        ctx.rotate(rot * Math.PI / 180);
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, -w0 * scale / 2, -h0 * scale / 2, w0 * scale, h0 * scale);

        var data = ctx.getImageData(0, 0, cw, ch);
        var px = data.data;
        var hist = new Uint32Array(256);
        var n = px.length / 4;
        var gray = new Uint8ClampedArray(n);
        for (var i = 0, p = 0; i < n; i++, p += 4) {
            var v = Math.max(px[p], px[p + 1], px[p + 2]);
            gray[i] = v;
            hist[v]++;
        }
        var lo = 0;
        var hi = 255;
        var acc = 0;
        for (i = 0; i < 256; i++) {
            acc += hist[i];
            if (acc > n * 0.01) {
                lo = i;
                break;
            }
        }
        acc = 0;
        for (i = 255; i >= 0; i--) {
            acc += hist[i];
            if (acc > n * 0.2) {
                hi = i;
                break;
            }
        }
        var range = Math.max(hi - lo, 1);
        for (i = 0, p = 0; i < n; i++, p += 4) {
            var g = (gray[i] - lo) * 255 / range;
            px[p] = px[p + 1] = px[p + 2] = g;
            px[p + 3] = 255;
        }
        ctx.putImageData(data, 0, 0);

        return canvas;
    }

    function recognize(base, canvas, psm, onProgress) {
        engine.progress = onProgress;

        return getWorker(base).then(function (worker) {
            return worker.setParameters({
                tessedit_pageseg_mode: String(psm),
                preserve_interword_spaces: '1',
                user_defined_dpi: '300',
            }).then(function () {
                return worker.recognize(canvas);
            });
        }).then(function (result) {
            engine.progress = null;

            return result.data.text || '';
        }, function (e) {
            engine.progress = null;
            throw e;
        });
    }


    // OCR umum: params Tesseract bebas, opsional mengembalikan baris + bbox.
    function ocr(base, canvas, params, withLines, onProgress) {
        engine.progress = onProgress || null;

        return getWorker(base).then(function (worker) {
            return worker.setParameters(Object.assign({
                tessedit_pageseg_mode: '6',
                preserve_interword_spaces: '1',
                user_defined_dpi: '300',
                tessedit_char_whitelist: '',
            }, params || {})).then(function () {
                return worker.recognize(canvas, {}, withLines ? { text: true, blocks: true } : { text: true });
            });
        }).then(function (result) {
            engine.progress = null;
            var data = result.data || {};
            var lines = [];
            (data.blocks || []).forEach(function (b) {
                (b.paragraphs || []).forEach(function (p) {
                    (p.lines || []).forEach(function (l) {
                        var words = (l.words || []).map(function (w) {
                            return { text: w.text, bbox: w.bbox, conf: w.confidence };
                        });
                        // susun ulang teks: celah kata yang lebar (kolom lain) jadi spasi panjang
                        var hgt = Math.max(8, l.bbox.y1 - l.bbox.y0);
                        var text = words.map(function (w, i) {
                            var gap = i ? w.bbox.x0 - words[i - 1].bbox.x1 : 0;

                            return (i ? (gap > hgt * 2.2 ? '      ' : ' ') : '') + w.text;
                        }).join('');
                        lines.push({ text: words.length ? text : (l.text || '').replace(/\n$/, ''), bbox: l.bbox, words: words });
                    });
                });
            });

            return { text: data.text || '', lines: lines, conf: data.confidence || 0 };
        }, function (e) {
            engine.progress = null;
            throw e;
        });
    }

    // OCR khusus angka satu baris: kembalikan deretan digit + confidence per digit (0-100).
    function ocrDigits(base, canvas, psm, onProgress) {
        engine.progress = onProgress || null;

        return getWorker(base).then(function (worker) {
            return worker.setParameters({
                tessedit_pageseg_mode: String(psm),
                preserve_interword_spaces: '1',
                user_defined_dpi: '300',
                tessedit_char_whitelist: '0123456789',
            }).then(function () {
                return worker.recognize(canvas, {}, { text: true, blocks: true });
            });
        }).then(function (result) {
            engine.progress = null;
            var data = result.data || {};
            var d = '';
            var conf = [];
            (data.blocks || []).forEach(function (b) {
                (b.paragraphs || []).forEach(function (p) {
                    (p.lines || []).forEach(function (l) {
                        (l.words || []).forEach(function (w) {
                            (w.symbols || []).forEach(function (s) {
                                if (/^\d$/.test(s.text)) {
                                    d += s.text;
                                    conf.push(s.confidence);
                                }
                            });
                        });
                    });
                });
            });
            var digits = String(data.text || '').replace(/\D/g, '');

            return { text: data.text || '', d: digits, conf: d === digits ? conf : null };
        }, function (e) {
            engine.progress = null;
            throw e;
        });
    }

    /* ------------------------------------------------------------------
     * Preprocessing gambar KTP
     * ------------------------------------------------------------------ */
    function newCanvas(w, h) {
        var c = document.createElement('canvas');
        c.width = Math.max(1, Math.round(w));
        c.height = Math.max(1, Math.round(h));

        return c;
    }

    function ctx2d(c) {
        return c.getContext('2d', { willReadFrequently: true });
    }

    // Gambar sumber (+ rotasi manual operator) ke canvas dengan lebar maksimum tertentu.
    function drawSource(img, rotation, maxW) {
        var rot = ((rotation % 360) + 360) % 360;
        var side = rot === 90 || rot === 270;
        var w0 = img.naturalWidth || img.width;
        var h0 = img.naturalHeight || img.height;
        var scale = Math.min(1, maxW / (side ? h0 : w0));
        var c = newCanvas((side ? h0 : w0) * scale, (side ? w0 : h0) * scale);
        var x = ctx2d(c);
        x.fillStyle = '#fff';
        x.fillRect(0, 0, c.width, c.height);
        x.translate(c.width / 2, c.height / 2);
        x.rotate(rot * Math.PI / 180);
        x.imageSmoothingQuality = 'high';
        x.drawImage(img, -w0 * scale / 2, -h0 * scale / 2, w0 * scale, h0 * scale);

        return c;
    }

    // Deteksi area kartu KTP (warna dasar biru) agar latar meja/tangan dibuang.
    function detectCard(c) {
        var w = c.width;
        var h = c.height;
        var px = ctx2d(c).getImageData(0, 0, w, h).data;
        var rows = new Uint32Array(h);
        var cols = new Uint32Array(w);
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var p = (y * w + x) * 4;
                var r = px[p];
                var g = px[p + 1];
                var b = px[p + 2];
                if (b > 110 && b - r > 28 && b >= g - 10) {
                    rows[y]++;
                    cols[x]++;
                }
            }
        }
        var run = function (arr) {
            var max = 0;
            var i;
            for (i = 0; i < arr.length; i++) {
                max = Math.max(max, arr[i]);
            }
            var th = max * 0.35;
            var best = null;
            var start = -1;
            for (i = 0; i <= arr.length; i++) {
                var on = i < arr.length && arr[i] > th;
                if (on && start < 0) {
                    start = i;
                }
                if (!on && start >= 0) {
                    if (!best || i - start > best[1] - best[0]) {
                        best = [start, i];
                    }
                    start = -1;
                }
            }

            return best;
        };
        var ry = run(rows);
        var rx = run(cols);
        if (!ry || !rx) {
            return null;
        }
        var bw = rx[1] - rx[0];
        var bh = ry[1] - ry[0];
        var area = (bw * bh) / (w * h);
        var ratio = bw / bh;
        if (area < 0.12 || area > 0.97 || ratio < 1.1 || ratio > 2.3) {
            return null;
        }
        var mx = bw * 0.03;
        var my = bh * 0.03;

        return {
            x: Math.max(0, rx[0] - mx), y: Math.max(0, ry[0] - my),
            w: Math.min(w, rx[1] + mx) - Math.max(0, rx[0] - mx), h: Math.min(h, ry[1] + my) - Math.max(0, ry[0] - my),
        };
    }

    // Kanal terang: latar biru & guilloche jadi terang, teks hitam tetap gelap.
    function grayOf(c) {
        var d = ctx2d(c).getImageData(0, 0, c.width, c.height).data;
        var n = c.width * c.height;
        var g = new Float32Array(n);
        for (var i = 0, p = 0; i < n; i++, p += 4) {
            g[i] = Math.max(d[p], d[p + 1], d[p + 2]) * 0.7 + (d[p] * 0.3 + d[p + 1] * 0.59 + d[p + 2] * 0.11) * 0.3;
        }

        return g;
    }

    // Rata-rata lokal (box blur) via integral image.
    function boxMean(g, w, h, r) {
        var I = new Float64Array((w + 1) * (h + 1));
        var x;
        var y;
        for (y = 0; y < h; y++) {
            var sum = 0;
            for (x = 0; x < w; x++) {
                sum += g[y * w + x];
                I[(y + 1) * (w + 1) + x + 1] = I[y * (w + 1) + x + 1] + sum;
            }
        }
        var out = new Float32Array(w * h);
        for (y = 0; y < h; y++) {
            var y0 = Math.max(0, y - r);
            var y1 = Math.min(h, y + r + 1);
            for (x = 0; x < w; x++) {
                var x0 = Math.max(0, x - r);
                var x1 = Math.min(w, x + r + 1);
                out[y * w + x] = (I[y1 * (w + 1) + x1] - I[y0 * (w + 1) + x1] - I[y1 * (w + 1) + x0] + I[y0 * (w + 1) + x0]) / ((x1 - x0) * (y1 - y0));
            }
        }

        return out;
    }

    function stretch(g, loPct, hiPct) {
        var hist = new Uint32Array(256);
        var n = g.length;
        var i;
        for (i = 0; i < n; i++) {
            hist[Math.max(0, Math.min(255, g[i] | 0))]++;
        }
        var lo = 0;
        var hi = 255;
        var acc = 0;
        for (i = 0; i < 256; i++) {
            acc += hist[i];
            if (acc > n * loPct) {
                lo = i;
                break;
            }
        }
        acc = 0;
        for (i = 255; i >= 0; i--) {
            acc += hist[i];
            if (acc > n * hiPct) {
                hi = i;
                break;
            }
        }
        var range = Math.max(hi - lo, 1);
        for (i = 0; i < n; i++) {
            g[i] = Math.max(0, Math.min(255, (g[i] - lo) * 255 / range));
        }

        return g;
    }

    function grayToCanvas(g, w, h) {
        var c = newCanvas(w, h);
        var x = ctx2d(c);
        var img = x.createImageData(w, h);
        for (var i = 0, p = 0; i < g.length; i++, p += 4) {
            img.data[p] = img.data[p + 1] = img.data[p + 2] = g[i];
            img.data[p + 3] = 255;
        }
        x.putImageData(img, 0, 0);

        return c;
    }

    // Kemiringan teks (derajat) lewat proyeksi baris piksel gelap.
    function estimateSkew(c) {
        var scale = Math.min(1, 700 / c.width);
        var s = newCanvas(c.width * scale, c.height * scale);
        ctx2d(s).drawImage(c, 0, 0, s.width, s.height);
        var w = s.width;
        var h = s.height;
        var g = grayOf(s);
        var mean = boxMean(g, w, h, 12);
        var xs = [];
        var ys = [];
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var i = y * w + x;
                if (g[i] < mean[i] * 0.8) {
                    xs.push(x - w / 2);
                    ys.push(y - h / 2);
                }
            }
        }
        if (xs.length < 200) {
            return 0;
        }
        var score = function (deg) {
            var a = deg * Math.PI / 180;
            var sin = Math.sin(a);
            var cos = Math.cos(a);
            var hist = new Float32Array(h * 2 + 2);
            for (var k = 0; k < xs.length; k++) {
                var yy = Math.round(ys[k] * cos - xs[k] * sin + h);
                if (yy >= 0 && yy < hist.length) {
                    hist[yy]++;
                }
            }
            var sum = 0;
            for (var j = 0; j < hist.length; j++) {
                sum += hist[j] * hist[j];
            }

            return sum;
        };
        var best = 0;
        var top = -1;
        var deg;
        for (deg = -15; deg <= 15; deg += 1) {
            var v = score(deg);
            if (v > top) {
                top = v;
                best = deg;
            }
        }
        var center = best;
        for (deg = center - 1; deg <= center + 1; deg += 0.2) {
            var v2 = score(deg);
            if (v2 > top) {
                top = v2;
                best = deg;
            }
        }

        return Math.round(best * 10) / 10;
    }

    function rotateCanvas(c, deg) {
        var a = deg * Math.PI / 180;
        var w = c.width;
        var h = c.height;
        var nw = Math.abs(w * Math.cos(a)) + Math.abs(h * Math.sin(a));
        var nh = Math.abs(w * Math.sin(a)) + Math.abs(h * Math.cos(a));
        var o = newCanvas(nw, nh);
        var x = ctx2d(o);
        x.fillStyle = '#fff';
        x.fillRect(0, 0, o.width, o.height);
        x.translate(o.width / 2, o.height / 2);
        x.rotate(a);
        x.drawImage(c, -w / 2, -h / 2);

        return o;
    }

    /**
     * Pipeline: crop kartu -> deskew -> skala -> grayscale -> koreksi cahaya
     * -> contrast stretch -> sharpening (varian A) -> binarisasi adaptif + buang
     * bintik (varian B). Dua varian dipakai bergantian/voting.
     */
    function preprocessKtp(img, rotation, dbg) {
        var src = drawSource(img, rotation, 2600);
        var info = {};
        var card = detectCard(src);
        if (card) {
            var cropped = newCanvas(card.w, card.h);
            ctx2d(cropped).drawImage(src, card.x, card.y, card.w, card.h, 0, 0, card.w, card.h);
            src = cropped;
            info.card = true;
        }
        var skew = estimateSkew(src);
        if (Math.abs(skew) >= 0.4) {
            src = rotateCanvas(src, -skew);
        }
        info.skew = skew;

        var targetW = 1600;
        var sc = targetW / src.width;
        var w = Math.round(src.width * sc);
        var h = Math.round(src.height * sc);
        var base = newCanvas(w, h);
        var bx = ctx2d(base);
        bx.imageSmoothingQuality = 'high';
        bx.drawImage(src, 0, 0, w, h);

        var g = grayOf(base);
        // koreksi cahaya tidak rata: bagi dengan latar lokal
        var bg = boxMean(g, w, h, Math.round(w / 25));
        var n = w * h;
        var i;
        for (i = 0; i < n; i++) {
            g[i] = Math.min(255, g[i] * 235 / Math.max(bg[i], 1));
        }
        stretch(g, 0.01, 0.35);
        // sharpening (unsharp mask)
        var bl = boxMean(g, w, h, 1);
        var A = new Float32Array(n);
        for (i = 0; i < n; i++) {
            A[i] = Math.max(0, Math.min(255, g[i] + 0.8 * (g[i] - bl[i])));
        }
        // binarisasi adaptif (Bradley) + buang bintik
        var mean = boxMean(g, w, h, Math.round(w / 45));
        var B = new Float32Array(n);
        for (i = 0; i < n; i++) {
            B[i] = g[i] < mean[i] * 0.85 ? 0 : 255;
        }
        var y;
        var x;
        for (y = 1; y < h - 1; y++) {
            for (x = 1; x < w - 1; x++) {
                i = y * w + x;
                if (B[i] === 0) {
                    var nb = (B[i - 1] === 0) + (B[i + 1] === 0) + (B[i - w] === 0) + (B[i + w] === 0) + (B[i - w - 1] === 0) + (B[i - w + 1] === 0) + (B[i + w - 1] === 0) + (B[i + w + 1] === 0);
                    if (nb <= 1) {
                        B[i] = 255;
                    }
                }
            }
        }
        var out = { A: grayToCanvas(A, w, h), B: grayToCanvas(B, w, h), w: w, h: h, info: info };
        if (dbg) {
            dbg.stage('1. Setelah crop kartu' + (info.card ? '' : ' (kartu tidak terdeteksi, pakai gambar penuh)') + ' & deskew ' + skew + '°', base);
            dbg.stage('2. Varian A: grayscale + koreksi cahaya + kontras + sharpening', out.A);
            dbg.stage('3. Varian B: binarisasi adaptif + buang bintik', out.B);
        }

        return out;
    }

    // Potong satu area, perbesar ke tinggi teks ideal, beri margin putih.
    function cropForOcr(c, r, tinggi) {
        var x0 = Math.max(0, Math.floor(r.x0));
        var y0 = Math.max(0, Math.floor(r.y0));
        var x1 = Math.min(c.width, Math.ceil(r.x1));
        var y1 = Math.min(c.height, Math.ceil(r.y1));
        if (x1 - x0 < 10 || y1 - y0 < 6) {
            return null;
        }
        var scale = Math.max(0.6, Math.min(4, (tinggi || 72) / (y1 - y0)));
        var padding = 16;
        var o = newCanvas((x1 - x0) * scale + padding * 2, (y1 - y0) * scale + padding * 2);
        var x = ctx2d(o);
        x.fillStyle = '#fff';
        x.fillRect(0, 0, o.width, o.height);
        x.imageSmoothingQuality = 'high';
        x.drawImage(c, x0, y0, x1 - x0, y1 - y0, padding, padding, (x1 - x0) * scale, (y1 - y0) * scale);

        return o;
    }

    /* ------------------------------------------------------------------
     * Region baris KTP dari bbox hasil OCR penuh
     * ------------------------------------------------------------------ */
    function lineHeight(l) {
        return Math.max(8, l.bbox.y1 - l.bbox.y0);
    }

    // Area nilai (setelah ":") pada satu baris, dipotong di celah kolom yang lebar.
    function valueRegion(line, valueX, W) {
        var hgt = lineHeight(line);
        var words = (line.words || []).filter(function (wd) {
            return wd.bbox.x0 >= valueX - hgt * 0.3 && !/^[:;"“”=.]+$/.test(wd.text);
        });
        var x1 = line.bbox.x1;
        if (words.length) {
            x1 = words[0].bbox.x1;
            for (var i = 1; i < words.length; i++) {
                if (words[i].bbox.x0 - words[i - 1].bbox.x1 > hgt * 2.2) {
                    break;
                }
                x1 = words[i].bbox.x1;
            }
        }

        return { x0: valueX - hgt * 0.4, x1: Math.min(W, x1 + hgt * 0.6), y0: line.bbox.y0 - hgt * 0.3, y1: line.bbox.y1 + hgt * 0.3 };
    }

    // Posisi-x kolom nilai: median tepi kanan ":" pada baris-baris KTP.
    function valueColumnX(lines) {
        var xs = [];
        lines.forEach(function (l) {
            var ws = l.words || [];
            for (var i = 0; i < ws.length; i++) {
                var t = ws[i].text;
                var ci = t.search(/[:;]/);
                if (ci !== -1 && ws[i].bbox.x0 < l.bbox.x0 + (l.bbox.x1 - l.bbox.x0) * 0.7) {
                    if (ci === t.length - 1 && ws[i + 1]) {
                        xs.push(ws[i + 1].bbox.x0);
                    } else {
                        var frac = (ci + 1) / t.length;
                        xs.push(ws[i].bbox.x0 + (ws[i].bbox.x1 - ws[i].bbox.x0) * frac);
                    }
                    break;
                }
            }
        });
        if (!xs.length) {
            return null;
        }
        xs.sort(function (a, b) {
            return a - b;
        });

        return xs[Math.floor(xs.length / 2)];
    }

    /* ------------------------------------------------------------------
     * Debug (hanya pengembangan; tampil di halaman, TIDAK ke log/console/server)
     * ------------------------------------------------------------------ */
    function Debugger() {
        this.stages = [];
        this.passes = [];
        this.crops = [];
        this.notes = [];
        this.t0 = Date.now();
    }
    Debugger.prototype.stage = function (name, canvas) {
        this.stages.push({ name: name, url: canvas.toDataURL('image/jpeg', 0.8) });
    };
    Debugger.prototype.pass = function (name, text, score) {
        this.passes.push({ name: name, text: text, score: score });
    };
    Debugger.prototype.crop = function (field, variant, canvas, text, candidates) {
        this.crops.push({ field: field, variant: variant, url: canvas ? canvas.toDataURL('image/png') : '', text: text, cand: candidates });
    };
    Debugger.prototype.note = function (text) {
        this.notes.push(text);
    };

    function maskDigits(text, show) {
        return show ? text : String(text || '').replace(/\d{6,}/g, function (m) {
            return m.length >= 10 ? m.substr(0, 4) + m.substr(4, m.length - 8).replace(/\d/g, '•') + m.substr(-4) : m;
        });
    }

    function renderDebug(panel, $form, dbg, result) {
        var $box = $('#sd-ocr-debug');
        if (!$box.length) {
            $box = $('<div id="sd-ocr-debug" class="col-md-12 sd-ocr-debug"></div>').insertBefore($form.children().first());
        }
        var draw = function () {
            var show = $box.find('.sd-ocr-debug-show').prop('checked');
            var h = '<div class="sd-ocr-debug-head"><b><i class="fa fa-bug"></i> DEBUG OCR</b> (hanya di browser ini, tidak dikirim/disimpan) '
                + '<label><input type="checkbox" form="sd-ocr-noform" class="sd-ocr-debug-show"' + (show ? ' checked' : '') + '> tampilkan angka lengkap</label></div>';
            h += '<p>Waktu total: ' + ((Date.now() - dbg.t0) / 1000).toFixed(1) + ' dtk</p>';
            h += '<details open><summary>Hasil preprocessing</summary>';
            dbg.stages.forEach(function (s) {
                h += '<figure><img src="' + s.url + '"><figcaption>' + esc(s.name) + '</figcaption></figure>';
            });
            h += '</details><details><summary>OCR raw text (' + dbg.passes.length + ' tahap)</summary>';
            dbg.passes.forEach(function (p) {
                h += '<p><b>' + esc(p.name) + '</b> skor=' + (p.score !== undefined ? p.score.toFixed(2) : '-') + '</p><pre>' + esc(maskDigits(p.text, show)) + '</pre>';
            });
            h += '</details><details open><summary>Crop per field</summary>';
            dbg.crops.forEach(function (c) {
                h += '<figure>' + (c.url ? '<img src="' + c.url + '">' : '<em>tidak ada area</em>') + '<figcaption><b>' + esc(c.field) + '</b> [' + esc(c.variant) + ']: <code>'
                    + esc(maskDigits(c.text, show)) + '</code>' + (c.cand ? ' → ' + esc(maskDigits(JSON.stringify(c.cand), show)) : '') + '</figcaption></figure>';
            });
            h += '</details><details open><summary>Keputusan & parsing</summary><ul>';
            dbg.notes.forEach(function (n) {
                h += '<li>' + esc(maskDigits(n, show)) + '</li>';
            });
            h += '</ul><pre>' + esc(maskDigits(JSON.stringify(result, null, 1), show)) + '</pre></details>';
            $box.html(h);
        };
        draw();
        $box.off('change.sddbg').on('change.sddbg', '.sd-ocr-debug-show', draw);
    }

    /* ------------------------------------------------------------------
     * Pipeline khusus NIK
     * Tidak bergantung pada label "NIK" terbaca: baris NIK dicari lewat
     * proyeksi piksel gelap di antara judul KTP dan baris Nama (baris NIK
     * adalah teks paling tinggi/tebal di area itu).
     * ------------------------------------------------------------------ */
    function otsu(vals) {
        var hist = new Float64Array(256);
        var n = vals.length;
        var i;
        for (i = 0; i < n; i++) {
            hist[vals[i] | 0]++;
        }
        var sum = 0;
        for (i = 0; i < 256; i++) {
            sum += i * hist[i];
        }
        var sumB = 0;
        var wB = 0;
        var best = 0;
        var th = 128;
        for (i = 0; i < 256; i++) {
            wB += hist[i];
            if (!wB) {
                continue;
            }
            var wF = n - wB;
            if (!wF) {
                break;
            }
            sumB += i * hist[i];
            var mB = sumB / wB;
            var mF = (sum - sumB) / wF;
            var between = wB * wF * (mB - mF) * (mB - mF);
            if (between > best) {
                best = between;
                th = i;
            }
        }

        return th;
    }

    function grayRegion(c, x0, y0, x1, y1) {
        x0 = Math.max(0, Math.floor(x0));
        y0 = Math.max(0, Math.floor(y0));
        x1 = Math.min(c.width, Math.ceil(x1));
        y1 = Math.min(c.height, Math.ceil(y1));
        var w = Math.max(1, x1 - x0);
        var h = Math.max(1, y1 - y0);
        var d = ctx2d(c).getImageData(x0, y0, w, h).data;
        var g = new Uint8ClampedArray(w * h);
        for (var i = 0, p = 0; i < g.length; i++, p += 4) {
            g[i] = d[p];
        }

        return { g: g, w: w, h: h, x0: x0, y0: y0 };
    }

    function runs(arr, th, minGap) {
        var out = [];
        var start = -1;
        var lastOn = -1;
        for (var i = 0; i <= arr.length; i++) {
            var on = i < arr.length && arr[i] > th;
            if (on) {
                if (start < 0) {
                    start = i;
                } else if (minGap && i - lastOn > minGap) {
                    out.push([start, lastOn + 1]);
                    start = i;
                }
                lastOn = i;
            } else if (start >= 0 && (!minGap || i - lastOn > minGap || i === arr.length)) {
                out.push([start, lastOn + 1]);
                start = -1;
            }
        }

        return out;
    }

    // Cari kotak baris angka NIK di dalam pita [y0,y1] (koordinat canvas varian A).
    function locateNik(A, band, valueX) {
        var R = grayRegion(A, 0, band.y0, A.width * 0.74, band.y1);
        var th = otsu(R.g);
        var rows = new Float32Array(R.h);
        var x;
        var y;
        for (y = 0; y < R.h; y++) {
            for (x = 0; x < R.w; x++) {
                if (R.g[y * R.w + x] < th) {
                    rows[y]++;
                }
            }
        }
        var maxRow = 0;
        for (y = 0; y < R.h; y++) {
            maxRow = Math.max(maxRow, rows[y]);
        }
        if (!maxRow) {
            return null;
        }
        // pilih pita baris dengan "massa" terbesar dan tinggi wajar (baris NIK = paling tebal)
        var best = null;
        runs(rows, maxRow * 0.12, 0).forEach(function (r) {
            var hgt = r[1] - r[0];
            if (hgt < 8) {
                return;
            }
            var mass = 0;
            for (var k = r[0]; k < r[1]; k++) {
                mass += rows[k];
            }
            var score = mass * Math.sqrt(hgt);
            if (!best || score > best.score) {
                best = { score: score, y0: r[0], y1: r[1] };
            }
        });
        if (!best) {
            return null;
        }
        var rh = best.y1 - best.y0;
        var cols = new Float32Array(R.w);
        for (y = best.y0; y < best.y1; y++) {
            for (x = 0; x < R.w; x++) {
                if (R.g[y * R.w + x] < th) {
                    cols[x]++;
                }
            }
        }
        // segmen horizontal; celah > 0.9 x tinggi baris memisahkan "NIK", ":", dan angka
        var segs = runs(cols, 0, Math.round(rh * 0.9)).filter(function (s) {
            return s[1] - s[0] > rh * 0.3;
        });
        if (valueX !== null && valueX !== undefined) {
            var kanan = segs.filter(function (s) {
                return s[1] > valueX;
            });
            if (kanan.length) {
                segs = kanan;
            }
        }
        var seg = segs.sort(function (a, b) {
            return (b[1] - b[0]) - (a[1] - a[0]);
        })[0];
        if (!seg || seg[1] - seg[0] < rh * 4) {
            return null;
        }

        return { x0: seg[0] - rh * 0.3, x1: seg[1] + rh * 0.3, y0: band.y0 + best.y0 - rh * 0.3, y1: band.y0 + best.y1 + rh * 0.3, rh: rh };
    }

    // Varian gambar untuk area NIK: gray+kontras+sharpen, Otsu, tebal, tipis.
    function nikVariants(A, box) {
        var R = grayRegion(A, box.x0, box.y0, box.x1, box.y1);
        var scale = Math.max(1, Math.min(5, 64 / Math.max(8, box.rh || (R.h / 1.6))));
        var src = newCanvas(R.w, R.h);
        var sctx = ctx2d(src);
        var id = sctx.createImageData(R.w, R.h);
        for (var i = 0, p = 0; i < R.g.length; i++, p += 4) {
            id.data[p] = id.data[p + 1] = id.data[p + 2] = R.g[i];
            id.data[p + 3] = 255;
        }
        sctx.putImageData(id, 0, 0);
        var w = Math.round(R.w * scale);
        var h = Math.round(R.h * scale);
        var up = newCanvas(w, h);
        var uctx = ctx2d(up);
        uctx.imageSmoothingQuality = 'high';
        uctx.drawImage(src, 0, 0, w, h);
        var g = grayOf(up);
        stretch(g, 0.02, 0.45);
        var bl = boxMean(g, w, h, 1);
        var G = new Float32Array(g.length);
        for (i = 0; i < g.length; i++) {
            G[i] = Math.max(0, Math.min(255, g[i] + 1.0 * (g[i] - bl[i])));
        }
        var th = otsu(G);
        var BIN = new Float32Array(g.length);
        for (i = 0; i < g.length; i++) {
            BIN[i] = G[i] < th ? 0 : 255;
        }
        var morph = function (src2, dark) {
            var o = new Float32Array(src2.length);
            for (var y = 0; y < h; y++) {
                for (var x = 0; x < w; x++) {
                    var v = dark ? 255 : 0;
                    for (var dy = -1; dy <= 1; dy++) {
                        for (var dx = -1; dx <= 1; dx++) {
                            var yy = Math.min(h - 1, Math.max(0, y + dy));
                            var xx = Math.min(w - 1, Math.max(0, x + dx));
                            var s = src2[yy * w + xx];
                            v = dark ? Math.min(v, s) : Math.max(v, s);
                        }
                    }
                    o[y * w + x] = v;
                }
            }

            return o;
        };
        var pad = function (arr) {
            var c = grayToCanvas(arr, w, h);
            var o = newCanvas(w + 32, h + 32);
            var x = ctx2d(o);
            x.fillStyle = '#fff';
            x.fillRect(0, 0, o.width, o.height);
            x.drawImage(c, 16, 16);

            return o;
        };

        var TEBAL = morph(BIN, true);
        // adaptive threshold: ambang = rata-rata lokal (jendela ~ tinggi angka) dikurangi offset,
        // tahan terhadap gradasi cahaya & motif latar yang berbeda di sepanjang baris
        var lm = boxMean(g, w, h, 38); // ~0.6 x tinggi baris setelah upscale (64px)
        var ADAPT = new Float32Array(g.length);
        for (i = 0; i < g.length; i++) {
            ADAPT[i] = g[i] < lm[i] - 12 ? 0 : 255;
        }
        var polos = pad(g); // hanya grayscale + kontras, tanpa sharpening
        var kecil = newCanvas(Math.round(polos.width * 0.6), Math.round(polos.height * 0.6));
        var kctx = ctx2d(kecil);
        kctx.imageSmoothingQuality = 'high';
        kctx.drawImage(polos, 0, 0, kecil.width, kecil.height);

        return { gray: pad(G), otsu: pad(BIN), tebal: pad(TEBAL), tipis: pad(morph(BIN, false)), adaptif: pad(ADAPT), polos: polos, kecil: kecil, raw: { G: G, BIN: BIN, TEBAL: TEBAL, w: w, h: h } };
    }

    /**
     * Segmentasi digit NIK dari crop biner (0=gelap). Angka "1" yang sempit dan
     * berulang ("1111") sering digabung/dibuang Tesseract saat membaca satu baris,
     * jadi digit dipisah dulu berdasarkan celah kolom.
     * Mengembalikan daftar [x0,x1] per glyph, atau null bila tidak meyakinkan.
     */
    function segmentGlyphs(bin, w, h) {
        var cols = new Uint16Array(w);
        var x;
        var y;
        for (x = 0; x < w; x++) {
            for (y = 0; y < h; y++) {
                if (bin[y * w + x] === 0) {
                    cols[x]++;
                }
            }
        }
        var segs = [];
        var start = -1;
        for (x = 0; x <= w; x++) {
            var on = x < w && cols[x] > 0;
            if (on && start < 0) {
                start = x;
            }
            if (!on && start >= 0) {
                segs.push({ x0: start, x1: x });
                start = -1;
            }
        }
        segs.forEach(function (s) {
            var top = h;
            var bot = -1;
            var ink = 0;
            for (var xx = s.x0; xx < s.x1; xx++) {
                for (var yy = 0; yy < h; yy++) {
                    if (bin[yy * w + xx] === 0) {
                        ink++;
                        top = Math.min(top, yy);
                        bot = Math.max(bot, yy);
                    }
                }
            }
            s.ink = ink;
            s.tinggi = bot - top + 1;
        });
        var median = function (arr) {
            var a = arr.slice().sort(function (p, q) {
                return p - q;
            });

            return a.length ? a[Math.floor(a.length / 2)] : 0;
        };
        var mh = median(segs.map(function (s) {
            return s.tinggi;
        }));
        // buang derau, titik dua, dan sisa garis latar (terlalu pendek / tintanya sedikit)
        var mi = median(segs.map(function (s) {
            return s.ink;
        }));
        segs = segs.filter(function (s) {
            return s.tinggi >= mh * 0.6 && s.ink >= mi * 0.12;
        });
        if (segs.length < 8) {
            return null;
        }
        // pecah segmen lebar (digit yang menempel) di titik kolom paling tipis
        // lebar acuan dari glyph lebar (persentil 75): angka "1" yang sempit tidak boleh jadi acuan
        var lebarList = segs.map(function (s) {
            return s.x1 - s.x0;
        }).sort(function (p, q) {
            return p - q;
        });
        var lebar = lebarList[Math.floor(lebarList.length * 0.75)];
        if (!lebar) {
            return null;
        }
        var hasil = [];
        segs.forEach(function (s) {
            var n = Math.round((s.x1 - s.x0) / lebar);
            if (n <= 1 || (s.x1 - s.x0) < lebar * 1.6) {
                hasil.push([s.x0, s.x1]);

                return;
            }
            var x0 = s.x0;
            for (var k = 1; k < n; k++) {
                var target = s.x0 + Math.round((s.x1 - s.x0) * k / n);
                var cut = target;
                var min = Infinity;
                for (var xx = target - Math.round(lebar * 0.3); xx <= target + Math.round(lebar * 0.3); xx++) {
                    if (xx > x0 && xx < s.x1 && cols[xx] < min) {
                        min = cols[xx];
                        cut = xx;
                    }
                }
                hasil.push([x0, cut]);
                x0 = cut;
            }
            hasil.push([x0, s.x1]);
        });

        return hasil;
    }

    // Susun ulang glyph dengan jarak lebar (spasi = 0.6 x tinggi) agar tidak tergabung.
    function spacedCanvas(arr, w, h, glyphs) {
        var gap = Math.round(h * 0.6);
        var total = glyphs.reduce(function (t, g) {
            return t + (g[1] - g[0]) + gap;
        }, gap);
        var o = newCanvas(total, h + 32);
        var c = ctx2d(o);
        c.fillStyle = '#fff';
        c.fillRect(0, 0, o.width, o.height);
        var src = grayToCanvas(arr, w, h);
        var x = gap;
        glyphs.forEach(function (g) {
            c.drawImage(src, g[0], 0, g[1] - g[0], h, x, 16, g[1] - g[0], h);
            x += (g[1] - g[0]) + gap;
        });

        return o;
    }

    function status2(ctx, t) {
        if (ctx.status) {
            ctx.status(t);
        }
    }

    async function readNik(ctx) {
        var pre = ctx.pre;
        var A = pre.A;
        var band;
        var nama = ctx.nama;
        var nikLine = ctx.nikLine;
        if (nikLine) {
            var h1 = lineHeight(nikLine);
            band = { y0: nikLine.bbox.y0 - h1 * 0.8, y1: nikLine.bbox.y1 + h1 * 0.8 };
        } else if (nama) {
            var h2 = lineHeight(nama);
            band = { y0: Math.max(ctx.headerBottom || 0, nama.bbox.y0 - h2 * 5), y1: nama.bbox.y0 - h2 * 0.15 };
        } else {
            band = { y0: (ctx.headerBottom || pre.h * 0.12), y1: pre.h * 0.42 };
        }
        band.y0 = Math.max(0, band.y0);
        band.y1 = Math.min(pre.h, band.y1);
        var box = band.y1 - band.y0 > 10 ? locateNik(A, band, ctx.valueX) : null;
        if (ctx.dbg) {
            ctx.dbg.note('NIK khusus: pita y ' + Math.round(band.y0) + '-' + Math.round(band.y1) + (box ? ', baris angka ditemukan x ' + Math.round(box.x0) + '-' + Math.round(box.x1) : ', baris angka TIDAK ditemukan'));
        }
        if (!box && ctx.region) {
            box = Object.assign({ rh: (ctx.region.y1 - ctx.region.y0) / 1.6 }, ctx.region);
        }
        if (!box) {
            return [];
        }
        var v = nikVariants(A, box);
        // Semua kombinasi preprocessing x PSM dibaca; hasilnya digabung per posisi digit di nikEnsemble().
        var jobs = [['gray', v.gray, '7'], ['otsu', v.otsu, '7'], ['tebal', v.tebal, '7'], ['tipis', v.tipis, '7'], ['adaptif', v.adaptif, '7'], ['polos', v.polos, '7'],
            ['kecil', v.kecil, '7'], ['gray kata', v.gray, '8'], ['polos kata', v.polos, '8'], ['otsu mentah', v.otsu, '13'], ['adaptif mentah', v.adaptif, '13'], ['gray mentah', v.gray, '13']];
        var hasil = [];
        for (var i = 0; i < jobs.length; i++) {
            var o = await ocrDigits(ctx.base, jobs[i][1], jobs[i][2], ctx.prog);
            var cand = nikCandidates(o.d, true);
            hasil.push({ nik: cand, mentah: [o.d], baca: [{ d: o.d, conf: o.conf }], grup: 'nik-khusus', nama: 'NIK khusus ' + jobs[i][0] + ' PSM ' + jobs[i][2] });
            if (ctx.dbg) {
                ctx.dbg.crop('NIK khusus', jobs[i][0] + ' PSM ' + jobs[i][2], jobs[i][1], o.d + ' (' + o.d.length + ' digit)' + (o.conf ? ' conf ' + o.conf.map(Math.round).join(',') : ''), cand);
            }
        }
        // Segmentasi per digit: gambar berspasi + OCR per karakter.
        var raw = v.raw;
        var glyphs = segmentGlyphs(raw.BIN, raw.w, raw.h);
        if (glyphs && glyphs.length === 17) {
            glyphs = glyphs.slice(1); // sisa ":" / tepi label di kiri
        }
        if (ctx.dbg) {
            ctx.dbg.note('Segmentasi digit NIK: ' + (glyphs ? glyphs.length + ' glyph' : 'gagal'));
        }
        if (glyphs && glyphs.length >= 14 && glyphs.length <= 20) {
            var spasiJobs = [['spasi otsu', raw.BIN, '7'], ['spasi tebal', raw.TEBAL, '7'], ['spasi gray', raw.G, '7'], ['spasi otsu', raw.BIN, '13']];
            for (var sj = 0; sj < spasiJobs.length; sj++) {
                var sc = spacedCanvas(spasiJobs[sj][1], raw.w, raw.h, glyphs);
                var os = await ocrDigits(ctx.base, sc, spasiJobs[sj][2], ctx.prog);
                var scand = nikCandidates(os.d, true);
                hasil.push({ nik: scand, mentah: [os.d], baca: [{ d: os.d, conf: os.conf }], grup: 'nik-spasi', nama: 'NIK ' + spasiJobs[sj][0] + ' PSM ' + spasiJobs[sj][2] });
                if (ctx.dbg) {
                    ctx.dbg.crop('NIK berspasi', spasiJobs[sj][0] + ' PSM ' + spasiJobs[sj][2], sc, os.d + ' (' + os.d.length + ' digit)', scand);
                }
            }
        }

        return hasil;
    }

    /* ------------------------------------------------------------------
     * Pipeline OCR KTP lengkap
     * ------------------------------------------------------------------ */
    async function runKtp(panel, dbg) {
        var base = panel.base;
        var status = function (t) {
            panel.setStatus(t + '…', 'busy');
        };
        var prog = function (label) {
            return function (m) {
                if (m.status === 'recognizing text') {
                    panel.setProgress(m.progress);
                } else {
                    panel.setStatus((PROGRESS_LABEL[m.status] || label) + '…', 'busy');
                }
            };
        };
        var scoreParsed = function (r) {
            var keys = ['nama', 'tempatlahir', 'tanggallahir', 'sex', 'agama', 'kawin', 'pekerjaan', 'alamat'];

            return (r.nikKandidat.length ? 2 : 0) + keys.filter(function (k) {
                return r[k];
            }).length + (r._align._score || 0) / 5;
        };

        status('Menyiapkan gambar (crop, deskew, kontras)');
        await new Promise(function (r) {
            setTimeout(r, 20);
        });
        var pre = preprocessKtp(panel.image.img, panel.rotation, dbg);

        // 1) OCR halaman penuh: varian A, lalu B / PSM lain bila hasil kurang.
        var tries = [['A', pre.A, '6'], ['B', pre.B, '6'], ['A', pre.A, '4']];
        var best = null;
        var halaman = [];
        for (var t = 0; t < tries.length; t++) {
            status('Membaca teks KTP (tahap ' + (t + 1) + ')');
            var res = await ocr(base, tries[t][1], { tessedit_pageseg_mode: tries[t][2] }, true, prog('Membaca teks'));
            var parsed = parseKtpLines(res.lines.length ? res.lines : res.text);
            var sc = scoreParsed(parsed);
            halaman.push({ parsed: parsed, nama: 'halaman penuh ' + tries[t][0] + ' PSM ' + tries[t][2] });
            if (dbg) {
                dbg.pass('Halaman penuh varian ' + tries[t][0] + ' PSM ' + tries[t][2], res.text, sc);
            }
            if (!best || sc > best.score) {
                best = { score: sc, parsed: parsed, variant: tries[t][0] };
            }
            if (best.score >= 10) {
                break;
            }
        }
        var r = best.parsed;
        var L = r._lines;
        var a = r._align;
        // Setiap pass halaman penuh yang sudah dibaca (dan tampil di debug) ikut menjadi sumber NIK & tanggal lahir,
        // bukan hanya pass dengan skor terbaik; tempat lahir tetap dari pass terbaik saja.
        var sumber = halaman.map(function (h) {
            var p = h.parsed;

            return { nik: p.nikKandidat, tanggal: p.tanggallahir, tempat: p === r ? p.tempatlahir : '', grup: 'halaman', nama: h.nama + (p === r ? ' (terbaik)' : '') };
        });

        // 2) OCR ulang per region (NIK, Tempat/Tgl Lahir, dll) dari bbox baris.
        var withBox = L.filter(function (l) {
            return l.src && l.src.bbox;
        }).map(function (l) {
            return l.src;
        });
        var valueX = valueColumnX(withBox);
        var lineOf = function (key) {
            return a[key] && L[a[key].line].src && L[a[key].line].src.bbox ? L[a[key].line].src : null;
        };
        var nama = lineOf('nama');
        if (valueX === null && nama) {
            valueX = nama.bbox.x0 + (nama.bbox.x1 - nama.bbox.x0) * 0.3;
        }
        var regions = {};
        if (valueX !== null) {
            ['nik', 'nama', 'ttl', 'wn', 'sex'].forEach(function (k) {
                var ln = lineOf(k);
                if (ln) {
                    regions[k] = valueRegion(ln, valueX, pre.w);
                }
            });
            // Perkiraan posisi bila barisnya tidak dikenali: NIK di atas Nama, TTL di bawah Nama.
            if (nama) {
                var hh = lineHeight(nama);
                if (!regions.nik) {
                    regions.nik = { x0: valueX - hh, x1: pre.w * 0.75, y0: nama.bbox.y0 - hh * 2.6, y1: nama.bbox.y0 - hh * 0.1 };
                    if (dbg) {
                        dbg.note('Baris NIK tidak dikenali, area diperkirakan di atas baris Nama');
                    }
                }
                if (!regions.ttl) {
                    regions.ttl = { x0: valueX - hh * 0.4, x1: pre.w * 0.7, y0: nama.bbox.y1 - hh * 0.1, y1: nama.bbox.y1 + hh * 1.5 };
                    if (dbg) {
                        dbg.note('Baris Tempat/Tgl Lahir tidak dikenali, area diperkirakan di bawah baris Nama');
                    }
                }
            }
        } else if (dbg) {
            dbg.note('Kolom nilai (posisi ":") tidak ditemukan, OCR per region dilewati');
        }

        var line7 = { tessedit_pageseg_mode: '7' };
        // Persempit area NIK ke kata yang mayoritas angka (buang titik dua, foto, derau).
        var nikLine = lineOf('nik');
        if (nikLine) {
            var dw = (nikLine.words || []).filter(function (wd) {
                return (wd.text.match(/\d/g) || []).length >= 4;
            });
            if (dw.length) {
                var nh = lineHeight(nikLine);
                regions.nik = { x0: dw[0].bbox.x0 - nh * 0.3, x1: dw[dw.length - 1].bbox.x1 + nh * 0.3, y0: nikLine.bbox.y0 - nh * 0.25, y1: nikLine.bbox.y1 + nh * 0.25 };
            }
        }
        if (regions.nik) {
            status('Membaca ulang NIK (khusus angka)');
            var nikJobs = [['A angka', pre.A, true, 72], ['B angka', pre.B, true, 72], ['A angka kecil', pre.A, true, 40], ['A teks', pre.A, false, 56]];
            for (var j = 0; j < nikJobs.length; j++) {
                var cc = cropForOcr(nikJobs[j][1], regions.nik, nikJobs[j][3]);
                if (!cc) {
                    continue;
                }
                var o = await ocr(base, cc, nikJobs[j][2] ? Object.assign({ tessedit_char_whitelist: '0123456789' }, line7) : line7, false, prog('Membaca NIK'));
                var cand = nikCandidates(o.text, nikJobs[j][2]);
                sumber.push({ nik: cand, mentah: [o.text.replace(/\D/g, '')], grup: 'crop-nik', kelompok: 'crop-' + nikJobs[j][0].charAt(0), nama: 'crop NIK ' + nikJobs[j][0] });
                if (dbg) {
                    dbg.crop('NIK', nikJobs[j][0], cc, o.text.trim(), cand);
                }
            }
        }
        status('Membaca NIK (pipeline khusus angka)');
        var judul = L.filter(function (l) {
            return l.src && l.src.bbox && /PROV|KAB|KOTA/i.test(l.text) && (!nama || l.src.bbox.y1 < nama.bbox.y0);
        });
        var nikKhusus = await readNik({
            pre: pre, base: base, dbg: dbg, prog: prog('Membaca NIK'), status: status, valueX: valueX, nama: nama, nikLine: nikLine, region: regions.nik,
            headerBottom: judul.length ? Math.max.apply(null, judul.map(function (l) {
                return l.src.bbox.y1;
            })) : null,
        });
        sumber = sumber.concat(nikKhusus);
        if (regions.ttl) {
            status('Membaca ulang Tempat/Tanggal Lahir');
            var ttlJobs = [['A', pre.A], ['B', pre.B]];
            for (var k = 0; k < ttlJobs.length; k++) {
                var tc = cropForOcr(ttlJobs[k][1], regions.ttl);
                if (!tc) {
                    continue;
                }
                var ot = await ocr(base, tc, line7, false, prog('Membaca TTL'));
                var txt = ot.text.replace(/\n/g, ' ').trim();
                var lv = splitLabelValue(txt);
                var parts = ttlParts(lv.label && similarity(lv.label, 'TEMPAT/TGL LAHIR') > 0.4 ? lv.value : txt.replace(/^[\s:;.]+/, ''));
                sumber.push({ tanggal: parts.tanggal, tempat: parts.tempat, grup: 'crop-ttl', nama: 'crop TTL ' + ttlJobs[k][0] });
                if (dbg) {
                    dbg.crop('Tempat/Tgl Lahir', ttlJobs[k][0], tc, txt, parts);
                }
            }
        }
        // Nama: OCR ulang barisnya lalu voting (versi dengan spasi diutamakan).
        if (regions.nama) {
            status('Membaca ulang Nama');
            var namaKand = [r.nama];
            var namaJobs = [['A', pre.A], ['B', pre.B]];
            for (var q = 0; q < namaJobs.length; q++) {
                var ncc = cropForOcr(namaJobs[q][1], regions.nama, 56);
                if (!ncc) {
                    continue;
                }
                var on = await ocr(base, ncc, line7, false, prog('Membaca nama'));
                var nlv = splitLabelValue(on.text.replace(/\n/g, ' ').trim());
                var nv = cleanName(nlv.label && similarity(nlv.label, 'NAMA') > 0.5 ? nlv.value : on.text.replace(/^[\s:;.]+/, ''));
                namaKand.push(nv);
                if (dbg) {
                    dbg.crop('Nama', namaJobs[q][0], ncc, on.text.trim(), nv);
                }
            }
            r.nama = pilihNama(namaKand);
        }
                // Field lain yang belum terbaca: coba sekali OCR per baris.
        if (!r.wn && regions.wn) {
            var wc = cropForOcr(pre.A, regions.wn);
            if (wc) {
                var ow = await ocr(base, wc, line7, false, prog('Membaca kewarganegaraan'));
                r.wn = parseWn(ow.text) || '';
                if (dbg) {
                    dbg.crop('Kewarganegaraan', 'A', wc, ow.text.trim(), r.wn);
                }
            }
        }
        if (!r.goldar && a.sex && L[a.sex.line].src && L[a.sex.line].src.bbox) {
            var sl = L[a.sex.line].src;
            var sh = lineHeight(sl);
            var gc = cropForOcr(pre.A, { x0: valueX !== null ? valueX : sl.bbox.x0, x1: Math.min(pre.w, sl.bbox.x1 + sh * 4), y0: sl.bbox.y0 - sh * 0.3, y1: sl.bbox.y1 + sh * 0.3 });
            if (gc) {
                var og = await ocr(base, gc, line7, false, prog('Membaca golongan darah'));
                var gm = og.text.match(/Da\s*r\s*a\s*\w?\s*[:;.]?\s*(.{0,4})/i);
                r.goldar = gm ? (parseGoldar(gm[1]) || '') : '';
                if (!r.sex) {
                    r.sex = parseSex(og.text) || '';
                }
                if (dbg) {
                    dbg.crop('Jenis Kelamin / Gol. Darah', 'A', gc, og.text.trim(), r.goldar);
                }
            }
        }

        var dec = decideKtp(sumber, { provinsi: r.provinsi });
        if (dbg) {
            sumber.forEach(function (s) {
                dbg.note('Sumber ' + s.nama + ': NIK=' + JSON.stringify(s.nik || []) + ' tgl=' + (s.tanggal || '-') + ' tempat=' + (s.tempat || '-') + ' grup=' + (s.grup || '-'));
            });
            dbg.note('Provinsi judul: ' + (r.provinsi || '-') + ' -> kode ' + (kodeProvinsi(r.provinsi) || '-') + '; skor NIK: ' + JSON.stringify(dec.nikInfo || {}));
            if (dec.ensemble) {
                dbg.note('Ensemble NIK: ' + dec.ensemble.nik + (dec.ensemble.yakin ? ' (yakin)' : ' (belum yakin: ' + dec.ensemble.alasan.join('; ') + ')') + ', bacaan=' + dec.ensemble.dipakai + ', identik antar-kelompok=' + dec.ensemble.identikGrup
                    + ', min kesepakatan=' + dec.ensemble.minShare + ', min kelompok=' + dec.ensemble.minGrup + ', per posisi=' + dec.ensemble.posisi.map(function (p) {
                    return p.d + ':' + Math.round(p.share * 100) + (p.d2 ? '/' + p.d2 : '');
                }).join(' ') + ', kandidat=' + dec.ensemble.kandidat.join(' | '));
            }
            dbg.note('Skor kandidat NIK (independen): ' + JSON.stringify(dec.nikPilih || []));
            dbg.note('Keputusan: NIK=' + (dec.nik || '(tidak diisi)') + (dec.nikHint ? ' kandidat=' + dec.nikHint : '') + ', tgl=' + (dec.tanggallahir || '-') + ', tempat=' + (dec.tempatlahir || '-') + ', catatan=' + JSON.stringify(dec.catatan) + ', keyakinan=' + JSON.stringify(dec.keyakinan));
        }
        r.nik = dec.nik;
        r.nikHint = dec.nikHint;
        r.nikHints = dec.nikHints || [];
        r.tanggallahir = dec.tanggallahir;
        r.tempatlahir = dec.tempatlahir;
        r.catatan = dec.catatan;
        r.keyakinan = dec.keyakinan || {};
        // Keyakinan per field dicatat terpisah (informasi saja, tidak mengubah pengisian field ini).
        r.keyakinan.nama = !r.nama ? 'tidak terbaca' : (typeof namaKand !== 'undefined' && namaKand.filter(function (n) {
            return norm(n).replace(/\s/g, '') === norm(r.nama).replace(/\s/g, '');
        }).length >= 2 ? 'tinggi' : 'rendah');
        r.keyakinan.sex = r.sex ? 'terbaca' : 'tidak terbaca';
        // Jenis kelamin dari NIK hanya sebagai cadangan bila teks tidak terbaca.
        if (!r.sex && r.nik) {
            r.sex = infoDariNik(r.nik).sex;
            r.catatan.sex = 'diambil dari NIK';
            r.keyakinan.sex = 'dari NIK';
        }
        delete r._lines;
        delete r._align;
        delete r.nikKandidat;
        delete r.nikInfo;

        return r;
    }

    window.addEventListener('pagehide', function () {
        if (engine.worker) {
            engine.worker.then(function (w) {
                w.terminate();
            }).catch(function () {});
            engine.worker = null;
        }
    });

    /* ------------------------------------------------------------------
     * Pengisian field form existing
     * ------------------------------------------------------------------ */
    function field($form, name) {
        return $form.find('[name="' + name + '"]').not('[type=hidden]').first();
    }

    function optionFor($select, text, minScore) {
        if (!text) {
            return null;
        }
        // Coba teks lengkap, lalu buang kata di belakang satu per satu (sisa kolom lain hasil OCR).
        var words = String(text).trim().split(/\s+/);
        for (var n = words.length; n >= 1; n--) {
            var found = optionForText($select, words.slice(0, n).join(' '), minScore);
            if (found !== null) {
                return found;
            }
            if (n <= Math.ceil(words.length / 2)) {
                break;
            }
        }

        return null;
    }

    function optionForText($select, text, minScore) {
        var best = null;
        var top = 0;
        $select.find('option').each(function () {
            var val = $(this).attr('value');
            if (val === undefined || val === '' || $(this).prop('disabled')) {
                return;
            }
            var s = similarity(text, $(this).text());
            if (s > top) {
                top = s;
                best = val;
            }
        });

        return top >= (minScore || 0.72) ? best : null;
    }

    function markFilled($el) {
        var $target = $el.hasClass('select2-hidden-accessible') ? $el.next('.select2') : $el;
        $target.addClass('sd-ocr-filled');
        $el.one('change.sdocr input.sdocr', function () {
            $target.removeClass('sd-ocr-filled');
        });
    }

    /**
     * Isi satu field existing. Mengembalikan true bila berhasil diisi.
     * Field yang tidak ada di form, readonly/disabled, atau nilainya tidak cocok dibiarkan.
     */
    function fill($form, name, value, opts) {
        opts = opts || {};
        if (value === undefined || value === null || value === '') {
            return false;
        }
        var $el = field($form, name);
        if (!$el.length || $el.prop('disabled')) {
            return false;
        }
        if ($el.is('select')) {
            var opt = optionFor($el, value, opts.minScore);
            if (opt === null) {
                return false;
            }
            $el.val(opt).trigger('change');
        } else {
            if ($el.prop('readonly')) {
                return false;
            }
            var picker = $el.data('DateTimePicker');
            if (picker && window.moment && opts.date) {
                picker.date(window.moment(value, 'DD-MM-YYYY'));
            }
            $el.val(value).trigger('change');
        }
        markFilled($el);

        return true;
    }

    function fillNik($form, nik) {
        if (!/^\d{16}$/.test(nik || '')) {
            return false;
        }
        var $cb = $form.find('#nik_sementara');
        if ($cb.length && $cb.prop('checked')) {
            $cb.prop('checked', false).trigger('change');
        }
        var $nik = $form.find('#nik');
        if (!$nik.length || $nik.prop('readonly')) {
            return false;
        }
        $nik.val(nik).trigger('change');
        markFilled($nik);

        return true;
    }

    // Pilih Dusun -> RW -> RT existing bila pasangan RT/RW hasil OCR cocok dengan tepat satu wilayah.
    function fillWilayah($form, rt, rw) {
        if (!rt || !rw) {
            return 'kosong';
        }
        var $rt = $form.find('#id_cluster');
        if (!$rt.length) {
            return 'kosong';
        }
        var same = function (a, b) {
            a = String(a || '').trim();
            b = String(b || '').trim();

            return a === b || (/^\d+$/.test(a) && /^\d+$/.test(b) && parseInt(a, 10) === parseInt(b, 10));
        };
        var hits = [];
        $rt.find('optgroup').each(function () {
            var parts = String($(this).attr('value') || '').split('__');
            if (!same(parts[1], rw)) {
                return;
            }
            $(this).find('option').each(function () {
                if (same($(this).text(), rt)) {
                    hits.push({ dusun: parts[0], rw: parts[0] + '__' + parts[1], id: $(this).attr('value') });
                }
            });
        });
        if (hits.length !== 1) {
            return hits.length ? 'ganda' : 'tidak_ada';
        }
        $form.find('#dusun').val(hits[0].dusun).trigger('change');
        $form.find('#rw').val(hits[0].rw).trigger('change');
        $rt.val(hits[0].id).trigger('change');
        [$form.find('#dusun'), $form.find('#rw'), $rt].forEach(markFilled);

        return 'ok';
    }

    /* ------------------------------------------------------------------
     * Cek NIK / No. KK ke database (hanya angka yang dikirim, bukan gambar)
     * ------------------------------------------------------------------ */
    function cekDatabase(url, niks, noKk) {
        niks = (niks || []).filter(function (n) {
            return /^\d{16}$/.test(n);
        });
        if (!niks.length && !/^\d{16}$/.test(noKk || '')) {
            return $.Deferred().resolve({ nik: {}, no_kk: null }).promise();
        }

        return $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $.param({ nik: niks, no_kk: noKk || '' }),
        }).then(function (res) {
            return res || { nik: {}, no_kk: null };
        }, function () {
            return $.Deferred().resolve({ nik: {}, no_kk: null, gagal: true }).promise();
        });
    }

    function validasiNik(nik) {
        nik = String(nik || '');
        if (!nik) {
            return 'NIK tidak terbaca';
        }
        if (!/^\d+$/.test(nik)) {
            return 'NIK harus berisi angka saja';
        }
        if (nik.length !== 16) {
            return 'NIK harus 16 digit (terbaca ' + nik.length + ' digit)';
        }

        return null;
    }

    /* ------------------------------------------------------------------
     * UI bersama
     * ------------------------------------------------------------------ */
    var PROGRESS_LABEL = {
        'loading tesseract core': 'Memuat mesin OCR',
        'initializing tesseract': 'Menyiapkan mesin OCR',
        'initialized tesseract': 'Menyiapkan mesin OCR',
        'loading language traineddata': 'Memuat model bahasa Indonesia',
        'loading language traineddata (from cache)': 'Memuat model bahasa Indonesia',
        'loaded language traineddata': 'Memuat model bahasa Indonesia',
        'initializing api': 'Menyiapkan pembaca teks',
        'initialized api': 'Menyiapkan pembaca teks',
        'recognizing text': 'Membaca teks dokumen',
    };

    function esc(text) {
        return $('<div>').text(text === undefined || text === null ? '' : String(text)).html();
    }

    function Panel($root) {
        this.$root = $root;
        this.base = $root.data('ocr-base');
        this.cekUrl = $root.data('cek-url');
        this.$file = $root.find('.sd-ocr-file');
        this.$status = $root.find('.sd-ocr-status');
        this.$bar = $root.find('.sd-ocr-bar > span');
        this.$preview = $root.find('.sd-ocr-preview');
        this.$result = $root.find('.sd-ocr-result');
        this.rotation = 0;
        this.image = null;
        this.busy = false;
    }

    Panel.prototype.setStatus = function (text, type) {
        this.$status.removeClass('is-error is-ok is-busy').addClass(type ? 'is-' + type : '').text(text || '').toggle(!!text);
    };

    Panel.prototype.setProgress = function (value) {
        this.$root.find('.sd-ocr-bar').toggle(value !== null);
        this.$bar.css('width', Math.round((value || 0) * 100) + '%');
    };

    Panel.prototype.clearImage = function () {
        if (this.image) {
            URL.revokeObjectURL(this.image.url);
        }
        this.image = null;
        this.rotation = 0;
        this.$preview.hide().find('img').attr('src', '');
    };

    Panel.prototype.bind = function (onRun) {
        var self = this;
        this.$root.on('click', '.sd-ocr-scan', function () {
            if (!self.busy) {
                self.$file.val('').trigger('click');
            }
        });
        this.$file.on('change', function () {
            var file = this.files && this.files[0];
            // Kosongkan input agar file tidak ikut terkirim/tersimpan dan bisa dipilih ulang.
            $(this).val('');
            if (!file) {
                return;
            }
            if (!/^image\//.test(file.type)) {
                self.setStatus('Format tidak didukung. Gunakan foto/scan JPG, PNG, atau WEBP.', 'error');

                return;
            }
            self.clearImage();
            self.$result.empty().hide();
            loadImage(file).then(function (loaded) {
                self.image = loaded;
                self.$preview.show().find('img').attr('src', loaded.url).css('transform', 'rotate(0deg)');
                onRun();
            }, function (e) {
                self.setStatus(e.message, 'error');
            });
        });
        this.$root.on('click', '.sd-ocr-rotate', function () {
            if (self.busy || !self.image) {
                return;
            }
            self.rotation = (self.rotation + 90) % 360;
            self.$preview.find('img').css('transform', 'rotate(' + self.rotation + 'deg)');
            onRun();
        });
        this.$root.on('click', '.sd-ocr-retry', function () {
            if (!self.busy && self.image) {
                onRun();
            }
        });
        this.$root.on('click', '.sd-ocr-clear', function () {
            if (self.busy) {
                return;
            }
            self.clearImage();
            self.$result.empty().hide();
            self.setStatus('');
            self.setProgress(null);
        });
    };

    Panel.prototype.run = function (targetWidth, psmList, parse, isGood) {
        var self = this;
        if (!this.image) {
            return $.Deferred().reject().promise();
        }
        this.busy = true;
        this.$root.addClass('is-busy');
        this.setStatus('Menyiapkan gambar…', 'busy');
        this.setProgress(0);

        var canvas = prepareCanvas(this.image.img, this.rotation, targetWidth);
        var onProgress = function (m) {
            var label = PROGRESS_LABEL[m.status] || 'Memproses';
            self.setStatus(label + '…', 'busy');
            if (m.status === 'recognizing text') {
                self.setProgress(m.progress);
            }
        };

        var attempt = function (i, bestSoFar) {
            return recognize(self.base, canvas, psmList[i], onProgress).then(function (text) {
                var parsed = parse(text);
                var best = !bestSoFar || isGood(parsed) > isGood(bestSoFar) ? parsed : bestSoFar;
                if (isGood(best) >= 1 || i + 1 >= psmList.length) {
                    return best;
                }
                self.setStatus('Mencoba ulang dengan mode baca lain…', 'busy');

                return attempt(i + 1, best);
            });
        };

        return attempt(0, null).then(function (res) {
            canvas.width = canvas.height = 0;
            self.busy = false;
            self.$root.removeClass('is-busy');
            self.setProgress(null);

            return res;
        }, function (e) {
            canvas.width = canvas.height = 0;
            self.busy = false;
            self.$root.removeClass('is-busy');
            self.setProgress(null);
            self.setStatus((e && e.message && /WebAssembly|Browser/.test(e.message)) ? e.message : 'OCR gagal diproses. Coba foto yang lebih jelas, lalu ulangi.', 'error');
            throw e;
        });
    };

    function statusBadge(info) {
        if (!info) {
            return '<span class="label label-default">Belum dicek</span>';
        }
        if (info.terdaftar) {
            return '<a class="label label-danger" target="_blank" rel="noopener" href="' + esc(info.url) + '">Sudah terdaftar' + (info.status ? ' (' + esc(info.status) + ')' : '') + '</a>';
        }

        return '<span class="label label-success">Belum terdaftar</span>';
    }

    /* ------------------------------------------------------------------
     * Smart OCR KTP (Form Biodata Penduduk)
     * ------------------------------------------------------------------ */
    function initKtp($root) {
        var panel = new Panel($root);
        var $form = $('#' + ($root.data('form') || 'mainform'));
        // Debug hanya bila diizinkan di view (data-debug-allowed="1") DAN URL memuat ?ocr_debug=1.
        var debugOn = String($root.data('debug-allowed')) === '1' && /[?&]ocr_debug=1(&|$)/.test(window.location.search);

        var selesai = function () {
            panel.busy = false;
            $root.removeClass('is-busy');
            panel.setProgress(null);
        };

        panel.bind(function () {
            if (panel.busy || !panel.image) {
                return;
            }
            panel.busy = true;
            $root.addClass('is-busy');
            panel.setProgress(0);
            $('#sd-ocr-debug').remove();
            var dbg = debugOn ? new Debugger() : null;
            runKtp(panel, dbg).then(function (data) {
                selesai();
                applyKtp(panel, $form, data);
                if (dbg) {
                    renderDebug(panel, $form, dbg, data);
                }
            }, function (e) {
                selesai();
                panel.setStatus((e && e.message && /WebAssembly|Browser/.test(e.message)) ? e.message : 'OCR gagal diproses. Coba foto yang lebih jelas, lalu ulangi.', 'error');
                if (dbg) {
                    dbg.note('Error: ' + (e && e.message ? e.message : e));
                    renderDebug(panel, $form, dbg, {});
                }
            });
        });
        $root.on('click', '.sd-ocr-clear', function () {
            $('#sd-ocr-debug').remove();
        });
    }

    function applyKtp(panel, $form, d) {
        var alamatLengkap = [
            d.alamat,
            d.rt || d.rw ? 'RT ' + (d.rt || '-') + '/RW ' + (d.rw || '-') : '',
            d.desa ? 'DESA/KEL. ' + d.desa : '',
            d.kecamatan ? 'KEC. ' + d.kecamatan : '',
            d.kabupaten,
            d.provinsi ? 'PROV. ' + d.provinsi : '',
        ].filter(Boolean).join(', ');

        var rows = [];
        var add = function (label, value, ok, note) {
            rows.push({ label: label, value: value, ok: ok, note: note });
        };

        var catatan = d.catatan || {};
        var nikError = validasiNik(d.nik);
        if (!d.nik && d.nikHint) {
            nikError = 'Belum yakin. Kandidat NIK: ' + (d.nikHints && d.nikHints.length ? d.nikHints.join(' / ') : d.nikHint) + ' — cocokkan dengan KTP lalu ketik manual';
        }
        add('NIK', d.nik, !validasiNik(d.nik) && fillNik($form, d.nik), nikError || (d.nik ? catatan.nik : ''));
        add('Nama', d.nama, fill($form, 'nama', d.nama));
        add('Tempat Lahir', d.tempatlahir, fill($form, 'tempatlahir', d.tempatlahir));
        add('Tanggal Lahir', d.tanggallahir, fill($form, 'tanggallahir', d.tanggallahir, { date: true }), catatan.tanggallahir);
        add('Jenis Kelamin', d.sex, fill($form, 'sex', d.sex), catatan.sex);
        add('Gol. Darah', d.goldar, fill($form, 'golongan_darah_id', d.goldar, { minScore: 0.99 }));
        add('Agama', d.agama, fill($form, 'agama_id', d.agama, { minScore: 0.6 }));
        add('Status Perkawinan', d.kawin, fill($form, 'status_kawin', d.kawin, { minScore: 0.99 }));
        add('Pekerjaan', d.pekerjaan, fill($form, 'pekerjaan_id', d.pekerjaan, { minScore: 0.7 }));
        add('Kewarganegaraan', d.wn, fill($form, 'warganegara_id', d.wn, { minScore: 0.99 }));

        // Alamat KTP pendatang = alamat asal -> isi "Alamat Sebelumnya" bila masih kosong.
        var $sebelum = field($form, 'alamat_sebelumnya');
        var alamatOk = false;
        if ($sebelum.length && !$sebelum.val()) {
            alamatOk = fill($form, 'alamat_sebelumnya', alamatLengkap.substr(0, 200));
        }
        add('Alamat (ke Alamat Sebelumnya)', alamatLengkap, alamatOk, $sebelum.length && $sebelum.val() && !alamatOk ? 'Field sudah berisi, tidak ditimpa' : null);

        var info = [];
        ['desa:Desa/Kel', 'kecamatan:Kecamatan', 'kabupaten:Kab/Kota', 'provinsi:Provinsi'].forEach(function (pair) {
            var p = pair.split(':');
            if (d[p[0]]) {
                info.push(esc(p[1]) + ': <b>' + esc(d[p[0]]) + '</b>');
            }
        });

        var filled = rows.filter(function (r) {
            return r.ok;
        }).length;

        var html = '<div class="sd-ocr-dup"></div>'
            + '<p class="sd-ocr-note"><i class="fa fa-info-circle"></i> ' + filled + ' field terisi otomatis (ditandai kuning). '
            + 'Periksa &amp; koreksi sebelum klik <b>Simpan</b>. Data belum disimpan.</p>'
            + '<ul class="sd-ocr-list">';
        rows.forEach(function (r) {
            html += '<li class="' + (r.ok ? 'ok' : 'miss') + '"><i class="fa ' + (r.ok ? 'fa-check-circle' : 'fa-minus-circle') + '"></i> '
                + '<span class="k">' + esc(r.label) + '</span>'
                + '<span class="v">' + (r.value ? esc(r.value) : '<em>tidak terbaca</em>') + '</span>'
                + (r.note ? '<small class="n">' + esc(r.note) + '</small>' : (!r.ok && r.value ? '<small class="n">Tidak cocok, isi manual</small>' : ''))
                + '</li>';
        });
        html += '</ul>';
        if (info.length) {
            html += '<p class="sd-ocr-meta">' + info.join('<br>') + '</p>';
        }
        panel.$result.html(html).show();
        panel.setStatus(filled ? 'Selesai. Silakan periksa hasilnya.' : 'Teks KTP tidak terbaca. Coba foto lebih terang/tegak, atau putar gambar.', filled ? 'ok' : 'error');

        if (!nikError) {
            cekDatabase(panel.cekUrl, [d.nik]).then(function (res) {
                var info = res.nik && res.nik[d.nik];
                var $dup = panel.$result.find('.sd-ocr-dup');
                if (info && info.terdaftar) {
                    $dup.html('<div class="alert alert-danger sd-ocr-alert"><i class="fa fa-exclamation-triangle"></i> <b>NIK sudah terdaftar</b> atas nama '
                        + esc(info.nama) + ' (' + esc(info.status) + '). <a href="' + esc(info.url) + '" target="_blank" rel="noopener">Lihat data</a>. '
                        + 'Jangan buat data ganda.</div>');
                    if (window.Swal) {
                        window.Swal.fire({ icon: 'warning', title: 'NIK sudah terdaftar', text: 'NIK hasil scan sudah terdaftar atas nama ' + info.nama + '. Periksa agar tidak terjadi data ganda.' });
                    }
                } else if (res.gagal) {
                    $dup.html('<div class="alert alert-warning sd-ocr-alert">Cek NIK ke database gagal. Validasi tetap dilakukan saat Simpan.</div>');
                } else {
                    $dup.html('<div class="alert alert-success sd-ocr-alert"><i class="fa fa-check"></i> NIK belum terdaftar.</div>');
                }
            });
        }
    }

    /* ------------------------------------------------------------------
     * Smart OCR KK (Form Keluarga)
     * ------------------------------------------------------------------ */
    var KK_COLS = [
        { key: 'nik', label: 'NIK', w: 150 },
        { key: 'nama', label: 'Nama', w: 170 },
        { key: 'sex', label: 'JK', w: 105 },
        { key: 'tempatlahir', label: 'Tempat Lahir', w: 120 },
        { key: 'tanggallahir', label: 'Tgl Lahir', w: 100 },
        { key: 'agama', label: 'Agama', w: 95 },
        { key: 'pendidikan', label: 'Pendidikan', w: 150 },
        { key: 'pekerjaan', label: 'Pekerjaan', w: 150 },
        { key: 'kawin', label: 'Status Kawin', w: 110 },
        { key: 'hubungan', label: 'Hubungan', w: 130 },
        { key: 'wn', label: 'WN', w: 60 },
        { key: 'goldar', label: 'Gol. Darah', w: 70 },
        { key: 'ayah', label: 'Nama Ayah', w: 150 },
        { key: 'ibu', label: 'Nama Ibu', w: 150 },
    ];

    function initKk($root) {
        var panel = new Panel($root);
        var $form = $('#' + ($root.data('form') || 'mainform'));
        var state = { data: null, cek: {} };

        var score = function (r) {
            var s = (/^\d{16}$/.test(r.no_kk) ? 0.4 : 0) + Math.min(r.anggota.length, 1) * 0.4;
            var lengkap = r.anggota.filter(function (a) {
                return a.nama && a.hubungan;
            }).length;

            return s + (r.anggota.length ? 0.2 * lengkap / r.anggota.length : 0);
        };

        panel.bind(function () {
            panel.run(3000, [6, 4], parseKk, function (r) {
                return score(r) >= 0.95 ? 1 : score(r);
            }).then(function (data) {
                state.data = data;
                state.cek = {};
                renderKk(panel, state);
                panel.setStatus(data.anggota.length || data.no_kk
                    ? 'Selesai. Periksa & koreksi tabel di bawah, lalu klik "Isi Form Keluarga".'
                    : 'Teks KK tidak terbaca. Coba scan lebih jelas/tegak, atau putar gambar.', data.anggota.length || data.no_kk ? 'ok' : 'error');
                cekKk(panel, state);
            });
        });

        $root.on('change', '.sd-ocr-kk-nokk', function () {
            state.data.no_kk = $(this).val().replace(/\s+/g, '');
            cekKk(panel, state);
        });
        $root.on('change', '.sd-ocr-kk-table input', function () {
            var $in = $(this);
            var i = $in.closest('tr').data('i');
            var key = $in.data('k');
            state.data.anggota[i][key] = $in.val().trim();
            if (key === 'nik') {
                cekKk(panel, state);
            }
        });
        $root.on('click', '.sd-ocr-kk-add', function () {
            state.data.anggota.push({});
            renderKk(panel, state);
        });
        $root.on('click', '.sd-ocr-kk-del', function () {
            state.data.anggota.splice($(this).closest('tr').data('i'), 1);
            renderKk(panel, state);
        });
        $root.on('click', '.sd-ocr-kk-apply', function () {
            applyKk(panel, $form, state);
        });
    }

    function cekKk(panel, state) {
        var d = state.data;
        if (!d) {
            return;
        }
        var niks = d.anggota.map(function (a) {
            return a.nik;
        });
        cekDatabase(panel.cekUrl, niks, d.no_kk).then(function (res) {
            state.cek = res.nik || {};
            state.noKk = res.no_kk;
            state.gagal = !!res.gagal;
            updateKkStatus(panel, state);
        });
    }

    function kepalaIndex(d) {
        var idx = -1;
        d.anggota.forEach(function (a, i) {
            if (idx === -1 && norm(a.hubungan) === 'KEPALAKELUARGA') {
                idx = i;
            }
        });
        if (idx === -1 && d.kepala) {
            d.anggota.forEach(function (a, i) {
                if (idx === -1 && similarity(a.nama, d.kepala) > 0.8) {
                    idx = i;
                }
            });
        }

        return idx === -1 && d.anggota.length ? 0 : idx;
    }

    function renderKk(panel, state) {
        var d = state.data;
        var kepala = state.kepala !== undefined && state.kepala < d.anggota.length ? state.kepala : kepalaIndex(d);
        state.kepala = kepala;

        var header = [
            ['Kepala Keluarga', d.kepala], ['Alamat', d.alamat], ['RT/RW', d.rt || d.rw ? (d.rt || '-') + '/' + (d.rw || '-') : ''],
            ['Desa/Kelurahan', d.desa], ['Kecamatan', d.kecamatan], ['Kabupaten/Kota', d.kabupaten], ['Provinsi', d.provinsi],
        ];

        var html = '<div class="sd-ocr-kk-head">'
            + '<div class="form-group"><label>Nomor KK (hasil OCR)</label>'
            + '<div class="input-group input-group-sm"><input type="text" class="form-control sd-ocr-kk-nokk" form="sd-ocr-noform" maxlength="16" value="' + esc(d.no_kk) + '">'
            + '<span class="input-group-addon sd-ocr-kk-nokk-status"><span class="label label-default">…</span></span></div>'
            + '<small class="sd-ocr-kk-nokk-err text-danger"></small></div>'
            + '<dl class="sd-ocr-kk-meta">';
        header.forEach(function (h) {
            html += '<dt>' + esc(h[0]) + '</dt><dd>' + (h[1] ? esc(h[1]) : '<em>tidak terbaca</em>') + '</dd>';
        });
        html += '</dl></div>';

        html += '<p class="sd-ocr-note"><i class="fa fa-users"></i> <b>' + d.anggota.length + ' anggota terbaca.</b> '
            + 'Semua sel dapat dikoreksi. Pilih <b>Kepala</b> untuk mengisi Data Kepala Keluarga pada form. '
            + 'Tidak ada data yang disimpan otomatis.</p>';

        html += '<div class="table-responsive sd-ocr-kk-wrap"><table class="table table-bordered table-condensed sd-ocr-kk-table"><thead><tr>'
            + '<th>No</th><th>Kepala</th><th>Status Data</th>';
        KK_COLS.forEach(function (c) {
            html += '<th style="min-width:' + c.w + 'px">' + esc(c.label) + '</th>';
        });
        html += '<th></th></tr></thead><tbody>';
        d.anggota.forEach(function (a, i) {
            html += '<tr data-i="' + i + '"' + (i === kepala ? ' class="is-kepala"' : '') + '><td>' + (i + 1) + '</td>'
                + '<td class="text-center"><input type="radio" name="sd_ocr_kepala_pilih" form="sd-ocr-noform" value="' + i + '"' + (i === kepala ? ' checked' : '') + '></td>'
                + '<td class="sd-ocr-kk-st"></td>';
            KK_COLS.forEach(function (c) {
                html += '<td><input type="text" class="form-control input-sm" form="sd-ocr-noform" data-k="' + c.key + '" value="' + esc(a[c.key] || '') + '"></td>';
            });
            html += '<td><button type="button" class="btn btn-xs btn-default sd-ocr-kk-del" title="Hapus baris dari pratinjau"><i class="fa fa-times"></i></button></td></tr>';
        });
        html += '</tbody></table></div>'
            + '<div class="sd-ocr-kk-actions">'
            + '<button type="button" class="btn btn-default btn-sm sd-ocr-kk-add"><i class="fa fa-plus"></i> Tambah Baris</button> '
            + '<button type="button" class="btn btn-primary btn-sm sd-ocr-kk-apply"><i class="fa fa-magic"></i> Isi Form Keluarga</button>'
            + '</div>'
            + '<div class="sd-ocr-kk-applied"></div>'
            + '<p class="sd-ocr-meta"><i class="fa fa-info-circle"></i> Anggota selain kepala keluarga ditambahkan setelah KK disimpan, '
            + 'melalui <b>Daftar Anggota Keluarga &rsaquo; Tambah Anggota</b> (mekanisme yang sudah ada). Anggota berstatus '
            + '<span class="label label-danger">Sudah terdaftar</span> tidak perlu ditambahkan lagi.</p>';

        panel.$result.html(html).show();
        panel.$result.find('input[name=sd_ocr_kepala_pilih]').on('change', function () {
            state.kepala = parseInt($(this).val(), 10);
            panel.$result.find('tr.is-kepala').removeClass('is-kepala');
            $(this).closest('tr').addClass('is-kepala');
        });
        updateKkStatus(panel, state);
    }

    function updateKkStatus(panel, state) {
        var d = state.data;
        var kkErr = d.no_kk ? (/^\d{16}$/.test(d.no_kk) ? null : 'Nomor KK harus 16 digit angka') : 'Nomor KK tidak terbaca';
        panel.$result.find('.sd-ocr-kk-nokk-err').text(kkErr || '');
        var $kkSt = panel.$result.find('.sd-ocr-kk-nokk-status');
        if (kkErr) {
            $kkSt.html('<span class="label label-warning">Periksa</span>');
        } else if (state.noKk && state.noKk.terdaftar) {
            $kkSt.html('<span class="label label-danger">No. KK sudah terdaftar</span>');
        } else if (state.noKk) {
            $kkSt.html('<span class="label label-success">Belum terdaftar</span>');
        } else {
            $kkSt.html('<span class="label label-default">' + (state.gagal ? 'Gagal cek' : '…') + '</span>');
        }

        panel.$result.find('.sd-ocr-kk-table tbody tr').each(function () {
            var a = d.anggota[$(this).data('i')] || {};
            var err = validasiNik(a.nik);
            $(this).find('.sd-ocr-kk-st').html(err ? '<span class="label label-warning" title="' + esc(err) + '">NIK tidak valid</span>' : statusBadge(state.cek[a.nik]));
        });
    }

    function applyKk(panel, $form, state) {
        var d = state.data;
        var k = d.anggota[state.kepala] || {};
        var hasil = [];
        var add = function (label, ok, value) {
            hasil.push({ label: label, ok: ok, value: value });
        };

        var $nokk = $form.find('#no_kk');
        var nokkOk = false;
        if (/^\d{16}$/.test(d.no_kk || '') && $nokk.length) {
            var $cb = $form.find('#nokk_sementara');
            if ($cb.prop('checked')) {
                $cb.prop('checked', false).trigger('change');
            }
            if (!$nokk.prop('readonly')) {
                $nokk.val(d.no_kk).trigger('change');
                markFilled($nokk);
                nokkOk = true;
            }
        }
        add('Nomor KK', nokkOk, d.no_kk);

        var nikError = validasiNik(k.nik);
        add('NIK Kepala', !nikError && fillNik($form, k.nik), k.nik);
        add('Nama', fill($form, 'nama', cleanName(k.nama || d.kepala)), k.nama || d.kepala);
        add('Jenis Kelamin', fill($form, 'sex', parseSex(k.sex) || k.sex), k.sex);
        add('Tempat Lahir', fill($form, 'tempatlahir', (k.tempatlahir || '').toUpperCase()), k.tempatlahir);
        add('Tanggal Lahir', fill($form, 'tanggallahir', (parseDate(k.tanggallahir || '') || {}).value, { date: true }), k.tanggallahir);
        add('Agama', fill($form, 'agama_id', k.agama, { minScore: 0.6 }), k.agama);
        add('Pendidikan', fill($form, 'pendidikan_kk_id', k.pendidikan, { minScore: 0.7 }), k.pendidikan);
        add('Pekerjaan', fill($form, 'pekerjaan_id', k.pekerjaan, { minScore: 0.7 }), k.pekerjaan);
        add('Status Perkawinan', fill($form, 'status_kawin', parseKawin(k.kawin || ''), { minScore: 0.99 }), k.kawin);
        add('Kewarganegaraan', fill($form, 'warganegara_id', parseWn(k.wn || ''), { minScore: 0.99 }), k.wn);
        add('Gol. Darah', fill($form, 'golongan_darah_id', parseGoldar(k.goldar || ''), { minScore: 0.99 }), k.goldar);
        add('Nama Ayah', fill($form, 'nama_ayah', cleanName(k.ayah)), k.ayah);
        add('Nama Ibu', fill($form, 'nama_ibu', cleanName(k.ibu)), k.ibu);
        add('Alamat KK', fill($form, 'alamat', (d.alamat || '').substr(0, 200)), d.alamat);

        var wil = fillWilayah($form, d.rt, d.rw);
        var wilNote = { ok: null, kosong: 'RT/RW tidak terbaca', ganda: 'RT/RW cocok dengan lebih dari satu dusun, pilih manual', tidak_ada: 'RT/RW tidak ada di data wilayah desa, pilih manual' }[wil];
        add('Dusun/RW/RT', wil === 'ok', (d.rt || d.rw) ? 'RT ' + (d.rt || '-') + '/RW ' + (d.rw || '-') + (wilNote ? ' — ' + wilNote : '') : '');

        var html = '<div class="alert alert-info sd-ocr-alert"><b>Form keluarga telah diisi.</b> Field yang terisi ditandai kuning. '
            + 'Periksa kembali, lengkapi field wajib lainnya, lalu klik <b>Simpan</b>.';
        if (state.noKk && state.noKk.terdaftar) {
            html += '<br><b class="text-danger"><i class="fa fa-exclamation-triangle"></i> Nomor KK sudah terdaftar — penyimpanan akan ditolak validasi.</b>';
        }
        if (k.nik && state.cek[k.nik] && state.cek[k.nik].terdaftar) {
            html += '<br><b class="text-danger"><i class="fa fa-exclamation-triangle"></i> NIK kepala keluarga sudah terdaftar atas nama ' + esc(state.cek[k.nik].nama)
                + '. Gunakan menu "Tambah KK dari penduduk yang ada" agar tidak terjadi data ganda.</b>';
        }
        html += '<ul class="sd-ocr-list sd-ocr-list-inline">';
        hasil.forEach(function (r) {
            html += '<li class="' + (r.ok ? 'ok' : 'miss') + '"><i class="fa ' + (r.ok ? 'fa-check-circle' : 'fa-minus-circle') + '"></i> ' + esc(r.label)
                + (!r.ok && r.value ? ' <small>(' + esc(r.value) + ')</small>' : '') + '</li>';
        });
        html += '</ul></div>';
        panel.$result.find('.sd-ocr-kk-applied').html(html);

        var $first = $form.find('.sd-ocr-filled').first();
        if ($first.length) {
            $('html, body').animate({ scrollTop: $first.offset().top - 120 }, 300);
        }
    }

    // Ekspos parser untuk pengujian (tanpa data pribadi).
    window.SmartOcr = { parseKtp: parseKtp, parseKk: parseKk, similarity: similarity, prepareCanvas: prepareCanvas };

    $(function () {
        $('.sd-ocr[data-ocr="ktp"]').each(function () {
            initKtp($(this));
        });
        $('.sd-ocr[data-ocr="kk"]').each(function () {
            initKk($(this));
        });
    });
})(window, document, window.jQuery);
