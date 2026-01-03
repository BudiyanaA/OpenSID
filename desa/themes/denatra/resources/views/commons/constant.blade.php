@php
if (!defined('THEME_LOADED')) {
    define('THEME_LOADED', true);
    if (!function_exists('cekVersiMinimal')) {
        function cekVersiMinimal($versiMinimal) {
            $versi = preg_replace("/[^0-9]/", "", ambilVersi());
            return $versi >= $versiMinimal;
        }
    }
    if (!function_exists('cekVersiMaksimal')) {
        function cekVersiMaksimal($versiMaksimal) {
            $versi = preg_replace("/[^0-9]/", "", ambilVersi());
            return $versi <= $versiMaksimal;
        }
    }
    if (!function_exists('cekKehadiran')) {
        function cekKehadiran($cekKeyHadir, $cekKethadir)
        {
            return theme_config($cekKeyHadir, $cekKethadir);
        }
    }
    if (!function_exists('cekKondisiPink')) {
        function cekKondisiPink()
        {
            return theme_config('color', 'pink');
        }
    }
    if (!function_exists('cekKondisiPrimary')) {
        function cekKondisiPrimary()
        {
            return theme_config('color', 'primary');
        }
    }
    if (!function_exists('cekFluid')) {
        function cekFluid()
        {
            return theme_config('fluid', false);
        }
    }
    if (!function_exists('fsize')) {
        function fsize($file){
            $a = array("B", "KB", "MB", "GB", "TB", "PB");
            $pos = 0;
            $size = filesize($file);
            while ($size >= 1024)
            {
                $size /= 1024;
                $pos++;
            }
            return round ($size,2)." ".$a[$pos];
        }
    }
    defined('THEME_NAME') or define('THEME_NAME', 'DeNatra');
    defined('THEME_VERSION') or define('THEME_VERSION', 'v112.02');
    defined('THEME_TIMESTAMP') or define('THEME_TIMESTAMP', '20251202');
    defined('IS_PREMIUM') or define('IS_PREMIUM', preg_match('/premium/', ambilVersi()));
    defined('IS_251010') or define('IS_251010', cekVersiMinimal(251010));
}
@endphp
