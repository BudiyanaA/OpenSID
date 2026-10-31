<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Services;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Pembersih HTML isi artikel: hanya tag struktur teks, tanpa atribut
 * (kecuali href http/https pada <a>). Mencegah skrip/atribut event dari
 * hasil AI maupun dari isian yang dikirim ke server.
 */
final class KontenHtml
{
    private const TAG = '<p><h2><h3><h4><ul><ol><li><strong><b><em><i><u><blockquote><br><a>';

    public static function bersihkan(string $html): string
    {
        // Buang elemen berbahaya beserta isinya sebelum strip_tags.
        $html = preg_replace('#<(script|style|iframe|object|embed|noscript|template)\b[^>]*>.*?</\1\s*>#is', '', $html);
        $html = strip_tags((string) $html, self::TAG);

        $html = preg_replace_callback('#<(/?)([a-z0-9]+)\b([^>]*)>#i', static function (array $m): string {
            $tag = strtolower($m[2]);

            if ('/' === $m[1]) {
                return "</{$tag}>";
            }

            if ('br' === $tag) {
                return '<br>';
            }

            if ('a' === $tag) {
                if (preg_match('#\bhref\s*=\s*(["\'])(https?://[^"\'\s<>]+)\1#i', $m[3], $href)) {
                    return '<a href="' . htmlspecialchars($href[2], ENT_QUOTES) . '" target="_blank" rel="noopener">';
                }

                return '<a>';
            }

            return "<{$tag}>";
        }, $html);

        return trim((string) $html);
    }
}
