<?php
// pakistan.php — Pakistan localization for the Expert Marketing & Developers port.

const PAK_CITIES = [
    'Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad',
    'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala',
    'Bahawalpur', 'Hyderabad', 'Sukkur', 'Sahiwal', 'Gujrat',
];

const PAK_AREAS = [
    'Downtown Dubai' => 'Clifton, Karachi',
    'Dubai Marina' => 'Defence, Karachi',
    'Palm Jumeirah' => 'DHA Phase 6, Karachi',
    'Business Bay' => 'Gulberg, Lahore',
    'Emaar Beachfront' => 'DHA Lahore',
    'Bluewaters Island Dubai' => 'Bahria Town, Rawalpindi',
    'Bluewater Island Dubai' => 'Bahria Town, Rawalpindi',
    'Jumeirah Lake Towers' => 'Johar Town, Lahore',
    'Dubai Creek Harbour' => 'Blue Area, Islamabad',
    'Sobha Hartland' => 'Bahria Town, Lahore',
    'Dubai Hills Estate' => 'DHA Phase 5, Lahore',
    'Jumeirah Beach Residence' => 'F-7, Islamabad',
    'Jumeirah Village Circle' => 'Model Town, Lahore',
    'Dubai South' => 'DHA Valley, Islamabad',
    'Dubai Sports City' => 'Cantt, Rawalpindi',
    'DIFC' => 'Karachi',
    'Emaar South' => 'Gulshan-e-Iqbal, Karachi',
    'Jumeirah Bay Island' => 'DHA Phase 8, Karachi',
    'DAMAC Hills 2' => 'Bahria Town, Karachi',
    'DAMAC Hills' => 'Bahria Town, Karachi',
    'Damac Hills 2' => 'Bahria Town, Karachi',
    'Damac Hills' => 'Bahria Town, Karachi',
    'Damac Islands' => 'Hyderabad',
    'Sobha Siniya Island' => 'DHA City, Karachi',
    'Al Marjan Island' => 'Gwadar',
    'Palm Jebel Ali' => 'Lahore',
    'Dubai Islands' => 'Islamabad',
    'Jumeirah Golf Estates' => 'DHA Phase 7, Lahore',
    'Mina Al Arab' => 'Karachi',
    'Marina Gate' => 'Defence, Karachi',
    'Meydan' => 'Gulistan-e-Jauhar, Karachi',
    'Arabian Ranches' => 'Bahria Town, Karachi',
    'Al Furjan' => 'Saddar, Rawalpindi',
    'Mirdif' => 'Satellite Town, Rawalpindi',
    'Deira' => 'Anarkali, Lahore',
    'Bur Dubai' => 'PECHS, Karachi',
    'Mohammed Bin Rashid City' => 'DHA Islamabad',
    'Expo City' => 'Expo City, Lahore',
];

function pak_area_slug_map(): array
{
    static $map = null;
    if ($map !== null) return $map;
    $map = [];
    foreach (PAK_AREAS as $from => $to) {
        $map[strtolower(preg_replace('/[^a-z0-9]+/i', '', $from))] = $to;
    }
    return $map;
}

function pak_area_name(string $s): string
{
    $s = trim((string) $s);
    if ($s === '') return 'Pakistan';
    $key = strtolower(preg_replace('/[^a-z0-9]+/i', '', $s));
    if (isset(pak_area_slug_map()[$key])) return pak_area_slug_map()[$key];
    foreach (PAK_AREAS as $from => $to) {
        if (stripos($s, $from) !== false) {
            return (string) preg_replace('/' . preg_quote($from, '/') . '/i', $to, $s);
        }
    }
    if (str_contains($s, '-') || str_contains($s, '_')) {
        return ucwords(str_replace(['-', '_'], ' ', $s));
    }
    return $s;
}

function pak_city(string $seed): string
{
    $seed = strtolower((string) preg_replace('/[^a-z0-9]/i', '', (string) $seed));
    if ($seed === '') return 'Pakistan';
    return PAK_CITIES[crc32($seed) % count(PAK_CITIES)];
}

function pak_replace(?string $t, string $seed = ''): string
{
    if ($t === null || $t === '') return $t;
    $out = pak_area_name($t);
    $out = str_ireplace([
        'Dubai, United Arab Emirates',
        'Dubai, UAE',
        'United Arab Emirates',
        'in the UAE',
        'the UAE',
        'UAE',
        'Emirates',
    ], 'Pakistan', $out);
    $region = $seed !== '' ? pak_area_name($seed) : 'Pakistan';
    $target = $seed !== '' ? 'in ' . $region : $region;
    $out = str_ireplace(['in Dubai', 'Dubai'], $target, $out);
    return $out;
}