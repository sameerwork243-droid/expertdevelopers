<?php
// functions.php — helpers (port of src/lib/utils.ts, src/lib/image.ts, src/lib/ref.ts, src/lib/store.ts)

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/pakistan.php';

if (!defined('APP_RAW_DIR')) {
    $rawCandidates = [
        dirname(__DIR__, 2) . '/data/raw',  // repo root (dev)
        __DIR__ . '/../data/raw',           // bundled with the PHP app (deploy)
    ];
    foreach ($rawCandidates as $c) {
        if (is_dir($c)) {
            define('APP_RAW_DIR', $c);
            break;
        }
    }
    if (!defined('APP_RAW_DIR')) define('APP_RAW_DIR', __DIR__ . '/../data/raw');
}
if (!defined('APP_ROOT')) define('APP_ROOT', dirname(__DIR__));

/* ---------------------------------- utils.ts ---------------------------------- */

function esc(?string $s): string
{
    if ($s === null) return '';
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_base_url(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'blowdryonwheels-com.wp-arenastaging.com';
    return 'https://' . $host;
}

/** Subdirectory the app is hosted under ('' at domain root). Set APP_BASE_PATH in .env to override auto-detect. */
function app_base_path(): string
{
    static $base = null;
    if ($base !== null) return $base;

    // Dedicated production domain serves the app at the domain root.
    $rootHosts = ['expertdevelopers.pk', 'www.expertdevelopers.pk'];
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    if (in_array($host, $rootHosts, true)) {
        $base = '';
        return $base;
    }

    $override = trim((string) env('APP_BASE_PATH', ''));
    if ($override !== '') {
        $base = '/' . trim($override, '/');
        return $base;
    }
    // Auto-detect: DOCUMENT_ROOT leaves the app at a subdirectory, e.g. /expertdevelopers
    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $scriptDir = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/');
    $scriptDir = $scriptDir === '' ? '/' : $scriptDir;
    if ($docRoot !== '' && str_starts_with($_SERVER['SCRIPT_FILENAME'] ?? '', $docRoot)) {
        $underRoot = dirname((string) $_SERVER['SCRIPT_FILENAME']);
        $rel = substr(rtrim(str_replace('\\', '/', $underRoot), '/'), strlen($docRoot));
        $base = $rel !== '' ? rtrim($rel, '/') : '';
        return $base;
    }
    $base = $scriptDir === '/' || $scriptDir === '/index.php' ? '' : $scriptDir;
    return $base;
}

/** Prefix a root-relative path with the app base path. */
function url(string $path): string
{
    if ($path === '' || $path === '/') return app_base_path() . '/';
    return app_base_path() . '/' . ltrim($path, '/');
}

/**
 * Output-buffer callback that rewrites root-absolute URL attributes (href="/…",
 * src="/…", action="/…", srcset, url('…')) to include the app base path.
 * Registry-authored JSON content that only uses relative/absolute paths is
 * handled here on the way out, so the rest of the port stays path-native.
 */
function rewrite_base_attributes(string $html): string
{
    $base = app_base_path();
    if ($base === '') return $html;
    $marker = $base . '/';
    $html = preg_replace_callback('#(href|src|srcset|action|poster|data-href|data-fs-href(?:-[a-z]+)?)="(/[^"]*)"#i', function ($m) use ($marker) {
        // skip root URL paths that are already base-prefixed
        if (str_starts_with($m[2], $marker)) return $m[0];
        return $m[1] . '="' . $marker . ltrim($m[2], '/') . '"';
    }, $html) ?? $html;
    // inline CSS url('/…') and url("/…")
    $html = preg_replace_callback('#url\(([\'"])/([^)]*?)\1\)#i', function ($m) use ($marker) {
        return 'url(' . $m[1] . $marker . ltrim($m[2], '/') . $m[1] . ')';
    }, $html) ?? $html;
    return $html;
}

function to_slug(string $s): string
{
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? $s;
    return trim($s, '-');
}

function fmt_price(float|int $value, string $currency = 'AED', float $rate = 1.0, int $digits = 0): string
{
    global $SETTINGS_SYMBOLS;
    $v = $value * $rate;
    $n = number_format($v, $digits, '.', ',');
    $sym = $SETTINGS_SYMBOLS[$currency] ?? (($currency === 'AED' ? 'AED' : $currency) . ' ');
    return $sym . $n;
}

function format_area(int|float $sqm, string $unit = 'Sqft'): string
{
    $v = $unit === 'Sqft' ? $sqm * 10.764 : $sqm;
    return number_format(round($v)) . ' ' . $unit;
}

/* ------------------------------ footer settings prefs ------------------------------ */

/** Settings cookie is "CUR|UNIT" e.g. "USD|Sqm". Values default to AED / Sqft. */
function expert_prefs(): array
{
    static $p = null;
    if ($p !== null) return $p;
    global $SETTINGS_CURRENCIES, $SETTINGS_UNITS;
    $p = ['cur' => 'PKR', 'unit' => 'Sqft'];
    $raw = trim((string) ($_COOKIE['expert_prefs'] ?? ''));
    if ($raw !== '') {
        $parts = explode('|', $raw);
        $cur = strtoupper(trim((string) ($parts[0] ?? '')));
        $unit = trim((string) ($parts[1] ?? ''));
        if (in_array($cur, $SETTINGS_CURRENCIES, true)) $p['cur'] = $cur;
        if ($unit === 'Sqm' || $unit === 'Sqft') $p['unit'] = $unit;
    }
    return $p;
}

/** Currency dropdown options: featured codes first, then the rest alphabetically. */
function expert_currency_options(): array
{
    global $SETTINGS_WORLD, $SETTINGS_FEATURED;
    $world = is_array($SETTINGS_WORLD) ? $SETTINGS_WORLD : [];
    $featured = is_array($SETTINGS_FEATURED) ? $SETTINGS_FEATURED : [];
    $opts = [];
    foreach ($featured as $code) {
        if (isset($world[$code])) $opts[] = ['label' => $code . ' - ' . $world[$code], 'code' => $code];
    }
    $rest = $world;
    foreach ($featured as $code) unset($rest[$code]);
    ksort($rest);
    foreach ($rest as $code => $name) $opts[] = ['label' => $code . ' - ' . $name, 'code' => $code];
    return $opts;
}

function expert_cur(): string { return expert_prefs()['cur']; }

function expert_unit(): string { return expert_prefs()['unit']; }

/**
 * Live base rates (1 AED = X) from open.er-api.com, cached to data/prefs-rates.json
 * for an hour, falling back to $SETTINGS_FALLBACK_RATES (or the stale cache) offline.
 */
function expert_rates(): array
{
    static $rates = null;
    if ($rates !== null) return $rates;

    global $SETTINGS_FALLBACK_RATES;
    $rates = is_array($SETTINGS_FALLBACK_RATES) ? $SETTINGS_FALLBACK_RATES : ['AED' => 1.0];

    $path = dirname(__DIR__) . '/data/prefs-rates.json';
    $cached = null;
    if (is_file($path)) {
        $j = json_decode((string) @file_get_contents($path), true);
        if (is_array($j) && isset($j['fetched'], $j['rates']) && is_array($j['rates'])) $cached = $j;
    }
    $fresh = false;
    if ($cached !== null && isset($cached['fetched']) && is_int($cached['fetched'])
        && time() - $cached['fetched'] < 3600) {
        $rates = array_merge($rates, $cached['rates']);
        return $rates;
    }

    $fetched = expert_fetch_rates();
    if (count($fetched) > 20) {
        $rates = array_merge($rates, $fetched);
        $dir = dirname($path);
        if (is_dir($dir)) {
            $tmp = $path . '.tmp';
            $json = json_encode(['fetched' => time(), 'rates' => $fetched], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json !== false && @file_put_contents($tmp, $json) !== false) @rename($tmp, $path);
        }
    } elseif ($cached !== null) {
        $rates = array_merge($rates, $cached['rates']);
    }
    return $rates;
}

/** Fetch 1-AED base rates from the free, keyless open.er-api.com endpoint. */
function expert_fetch_rates(): array
{
    $rates = [];
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 8,
            'header' => "User-Agent: Mozilla/5.0 (compatible; expertmarketingdevelopers)\r\nAccept: application/json\r\n",
        ],
    ]);
    $body = @file_get_contents('https://open.er-api.com/v6/latest/AED', false, $ctx);
    if ($body !== false) {
        $j = json_decode($body, true);
        if (is_array($j) && isset($j['result'], $j['rates'])
            && $j['result'] === 'success' && is_array($j['rates'])) {
            $rates = array_map(static fn ($v) => (float) $v, $j['rates']);
        }
    }
    return $rates;
}

function expert_rate(): float
{
    return (float) (expert_rates()[expert_cur()] ?? 1.0);
}

function expert_symbol(): string
{
    global $SETTINGS_SYMBOLS;
    $cur = expert_cur();
    return (string) ($SETTINGS_SYMBOLS[$cur] ?? ($cur === 'AED' ? 'AED ' : $cur . ' '));
}

function expert_sqf(int|float|null $feet): int
{
    $v = (float) ($feet ?? 0);
    return expert_unit() === 'Sqm' ? (int) round($v * 0.09290304) : (int) round($v);
}

/** Price text converted to the active currency, e.g. "$819,000". */
function expert_price_text(int|float $aed, string $qualifier = ''): string
{
    $v = (float) $aed * expert_rate();
    $n = number_format(round($v), 0, '.', ',');
    $prefix = $qualifier !== '' ? $qualifier . ' ' : expert_symbol();
    return $prefix . $n;
}

/** Area text converted to the active unit, e.g. "2,787 sq ft" / "259 sq m". */
function expert_area_text(int|float $feet): string
{
    return number_format(expert_sqf($feet)) . (expert_unit() === 'Sqm' ? ' sq m' : ' sq ft');
}

/** data-aed attribute (kept so JS re-converts instantly on dropdown change). */
function data_aed(?int $v): string
{
    return $v !== null && $v > 0 ? ' data-aed="' . (int) $v . '"' : '';
}

/** data-sqft attribute (value is /always/ stored in square feet). */
function data_sqft(?int $v): string
{
    return $v !== null && $v > 0 ? ' data-sqft="' . (int) $v . '"' : '';
}

/** AED value from display_price ("3,000,000", "1.9M", "450K") or a fallback price. */
function project_price_aed(string $dp, int $fallback = 0): int
{
    $dp = trim($dp);
    $fallback = (int) $fallback;
    if ($dp === '') return $fallback > 0 ? $fallback : 0;
    if (preg_match('/^[\d,]+$/', $dp)) return (int) str_replace(',', '', $dp);
    if (preg_match('/^([\d.]+)M$/i', $dp, $m)) return (int) round((float) $m[1] * 1000000);
    if (preg_match('/^([\d.]+)K$/i', $dp, $m)) return (int) round((float) $m[1] * 1000);
    return $fallback > 0 ? $fallback : 0;
}

/** Project "Starting Price" snippet: converts when display_price is a recognisable number. */
function project_price_markup(array $p): string
{
    $dp = (string) ($p['display_price'] ?? '');
    $num = project_price_aed($dp, (int) ($p['price'] ?? 0));
    if ($num > 0) {
        return '<div class="price"><span>Starting Price </span><span class="js-convert-price"' . data_aed($num) . '>' . esc(expert_price_text($num)) . '</span></div>';
    }
    $txt = $dp !== '' ? esc($dp) : '&#8212;';
    return '<div class="price"><span>Starting Price </span>' . $txt . '</div>';
}

/** Converted project price span, or the raw display string when not convertible. */
function project_price_text(string $dp, int $fallback = 0): string
{
    $num = project_price_aed($dp, $fallback);
    return $num > 0 ? '<span class="js-convert-price"' . data_aed($num) . '>' . esc(expert_price_text($num)) . '</span>' : esc($dp === '' ? (string) $fallback : $dp);
}

function wa_link(string $phone, string $text): string
{
    $clean = preg_replace('/[^0-9]/', '', $phone);
    return 'https://wa.me/' . $clean . '?text=' . rawurlencode($text);
}

function mailto_link(string $email, string $subject, string $body = ''): string
{
    $q = http_build_query(['subject' => $subject] + ($body !== '' ? ['body' => $body] : []));
    return 'mailto:' . $email . '?' . $q;
}

function parse_id_from_slug(string $slug): int
{
    if (preg_match('/(\d+)-?$/', $slug, $m)) return (int) $m[1];
    return 0;
}

/** FAQPage schema.org JSON-LD */
function faq_schema(array $items): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($i) => [
            '@type' => 'Question',
            'name' => $i['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['a']],
        ], $items),
    ];
}

/* ---------------------------------- image.ts ---------------------------------- */

function local_image(?string $url): ?string
{
    global $LOCAL_OVERRIDES;
    if (!$url) return null;
    if (!preg_match('/([^\/]+)\.(?:webp|jpe?g|png|avif)$/i', $url, $m)) return null;
    return $LOCAL_OVERRIDES[$m[1]]['base'] ?? null;
}

function local_image_for(?string $url, string $size): ?string
{
    global $LOCAL_OVERRIDES;
    if (!$url) return null;
    if (!preg_match('/([^\/]+)\.(?:webp|jpe?g|png|avif)$/i', $url, $m)) return null;
    $o = $LOCAL_OVERRIDES[$m[1]] ?? null;
    if (!$o) return null;
    return $o['sizes'][$size] ?? $o['base'];
}

function cf_path(string $rest, string $size): string
{
    $file = preg_replace('/\.(svg|png|jpe?g|avif)$/i', '.webp', $rest);
    $i = strrpos($file, '/');
    if ($i !== false) return CDN_BASE . '/x/' . substr($file, 0, $i) . '/' . $size . '/' . substr($file, $i + 1);
    return CDN_BASE . '/x/' . $size . '/' . $file;
}

/** ggfx S3 -> cloudfront transform URL (cft port). */
function cft(?string $url, int $w = 340, int $h = 252): string
{
    if (!$url) return '';
    $local = local_image_for($url, "{$w}x{$h}");
    if ($local) return $local;
    if (str_contains($url, CDN_BASE)) return $url;
    if (!preg_match('#/i/(.+)$#', $url, $m)) return $url;
    if (strpos($m[1], '/') === false) return CDN_BASE . '/i/' . $m[1];
    return cf_path($m[1], "{$w}x{$h}");
}

/** Width-only cloudfront transform (cfw port). */
function cfw(?string $url, int $w = 744): string
{
    if (!$url) return '';
    $local = local_image_for($url, "{$w}x");
    if ($local) return $local;
    if (str_contains($url, CDN_BASE)) return $url;
    if (!preg_match('#/i/(.+)$#', $url, $m)) return $url;
    if (strpos($m[1], '/') === false) return CDN_BASE . '/i/' . $m[1];
    return cf_path($m[1], "{$w}x");
}

/** Raw cloudfront /i/ URL (no transform) — root-level keys 403 on /x/ transforms. */
function cf_original(?string $url): string
{
    if (!$url) return '';
    if (str_contains($url, CDN_BASE)) return $url;
    return $url;
}

/** Legacy cf() helper from ref.ts (passthrough). */
function cf(?string $url, int $width = 340): ?string
{
    return $url ?: null;
}

/* ---------------------------------- ref.ts ---------------------------------- */

function load_json(string $rel): ?array
{
    $file = APP_RAW_DIR . '/' . ltrim($rel, '/');
    if (!is_file($file)) return null;
    $raw = file_get_contents($file);
    if ($raw === false) return null;
    $j = json_decode($raw, true, 2048);
    return is_array($j) ? $j : null;
}

/** Load page-data for a route (getPageData port). Returns raw JSON or null. */
function get_page_data(string $route): ?array
{
    $rel = $route === '/' ? 'index' : ltrim($route, '/');
    $rel = preg_replace('#/$#', '', $rel);
    $candidates = [
        'pages/' . $rel . '.json',
        'listings/' . $rel . '.json',
        'listings/' . $rel,
        'projects/' . $rel . '.json',
        'projects/' . $rel,
        'properties/' . $rel . '.json',
        'properties/' . $rel,
    ];
    // /new-projects/* are project pages; the properties/ snapshots are stale
    // scrapes (property-details responses) and must not shadow them.
    if (str_starts_with($route, '/new-projects')) {
        $candidates = array_values(array_filter($candidates, fn ($c) => !str_starts_with($c, 'properties/')));
    }
    foreach ($candidates as $c) {
        $j = load_json($c);
        if ($j) return $j;
    }
    return null;
}

/** Classify a page-data payload by template (classify port). */
function classify(?array $j, string $route): ?array
{
    if (!$j || !isset($j['result'])) return null;
    $res = $j['result'];
    $data = $res['data'] ?? [];
    $keys = array_keys($data);
    if (isset($res['serverData']) && is_array($res['serverData'])) {
        $sd = $res['serverData'];
        $inner = $sd['data'] ?? $sd;
        if (is_array($inner) && ($inner['status'] ?? null) === true && isset($inner['hits'])) {
            $h0 = $inner['hits'][0] ?? [];
            $kind = isset($h0['slug'])
                ? (str_starts_with($route, '/new-projects') ? 'project' : 'listing')
                : ((isset($h0['building_type']) || isset($h0['search_type'])) ? 'project' : 'listing');
            return ['kind' => $kind, 'data' => $inner, 'route' => $route];
        }
        if (is_array($inner) && ($inner['status'] ?? null) === true && isset($inner['data']) && isset($inner['message'])) {
            $d = $inner['data'];
            $isProject = (is_array($d) && ($d['department'] ?? '') === 'new_developments')
                || (is_array($d) && is_string($d['building_type'] ?? null) && isset($d['completion_year']));
            if ($isProject) return ['kind' => 'project', 'data' => ['hits' => [$d]], 'route' => $route];
            return ['kind' => 'property', 'data' => $d, 'route' => $route];
        }
    }
    foreach (['strapiPage', 'strapiBlog', 'strapiTeam', 'strapiAreaGuide', 'strapiCareer', 'strapiEvent', 'strapiDeveloper', 'strapiOffice'] as $key) {
        if (in_array($key, $keys, true)) return ['kind' => 'page', 'data' => $data[$key], 'route' => $route];
    }
    return null;
}

/* ---------------------------------- store.ts ---------------------------------- */

function load_rel(string $rel): ?array
{
    return load_json($rel . '.json');
}

function get_listing(string $rel): ?array
{
    $j = load_rel('listings/' . $rel);
    if (!$j || !isset($j['result'])) return null;
    $res = $j['result'];
    $sd = $res['serverData'] ?? null;
    if (!is_array($sd)) return null;
    $inner = $sd['data'] ?? $sd;
    return (is_array($inner) && isset($inner['hits'])) ? $inner : null;
}

function get_property(string $rel): ?array
{
    $j = load_rel('properties/' . $rel);
    if (!$j || !isset($j['result'])) return null;
    $res = $j['result'];
    $sd = $res['serverData'] ?? null;
    if (!is_array($sd)) return null;
    $inner = $sd['data'] ?? $sd;
    if (!is_array($inner)) return null;
    if (isset($inner['data']) && is_array($inner['data']) && ($inner['status'] ?? null) === true) return $inner['data'];
    return $inner;
}

/* ---------------------------------- misc ---------------------------------- */

/** Generic JSON response helper for api/*.php endpoints. */
function json_response(mixed $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function json_body(): array
{
    if (isset($_POST['json']) && is_string($_POST['json']) && $_POST['json'] !== '') {
        $j = json_decode($_POST['json'], true);
        if (is_array($j)) return $j;
    }
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return [];
    if (preg_match('/(?:^|&)json=/', $raw)) {
        parse_str($raw, $fields);
        if (isset($fields['json']) && is_string($fields['json']) && $fields['json'] !== '') {
            $j = json_decode($fields['json'], true);
            if (is_array($j)) return $j;
        }
    }
    $j = json_decode($raw, true);
    return is_array($j) ? $j : [];
}

/** Alt text convention: "{label} - Expert Marketing & Developers" */
function alt_text(string $label): string
{
    return trim($label) . ' - ' . APP_NAME;
}

function now_iso(): string
{
    return gmdate('Y-m-d\TH:i:s.v\Z');
}

/**
 * Apply the Expert Marketing & Developers logo as a top-left watermark to an image.
 * Works with JPEG, PNG, WebP. Preserves original format and aspect ratio.
 * Returns true on success, false on failure. Errors are logged but not exposed.
 */
function apply_logo_watermark(string $imagePath): bool
{
    $logoPath = __DIR__ . '/../images/expertlogo.png';
    if (!file_exists($logoPath)) { error_log('Watermark: expertlogo.png not found'); return false; }
    if (!file_exists($imagePath)) { error_log('Watermark: image not found: ' . $imagePath); return false; }

    $logoInfo = @getimagesize($logoPath);
    if ($logoInfo === false) { error_log('Watermark: cannot read expertlogo.png'); return false; }

    $imageInfo = @getimagesize($imagePath);
    if ($imageInfo === false) { error_log('Watermark: cannot read image'); return false; }

    $mime = $imageInfo['mime'] ?? '';
    $ext = match($mime) {
        'image/jpeg' => 'jpeg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => null,
    };
    if ($ext === null) { error_log('Watermark: unsupported mime ' . $mime); return false; }

    $srcFunc = match($ext) {
        'jpeg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
        'webp' => 'imagecreatefromwebp',
    };
    $dstFunc = match($ext) {
        'jpeg' => 'imagejpeg',
        'png' => 'imagepng',
        'webp' => 'imagewebp',
    };

    $src = @$srcFunc($imagePath);
    if (!$src) { error_log('Watermark: cannot create image resource'); return false; }
    $sw = imagesx($src); $sh = imagesy($src);
    if ($sw < 50 || $sh < 50) { imagedestroy($src); return false; }

    $logoW = $logoInfo[0]; $logoH = $logoInfo[1];
    $maxLogoW = (int)($sw * 0.25);
    $maxLogoH = (int)($sh * 0.12);
    $scale = min($maxLogoW / max($logoW, 1), $maxLogoH / max($logoH, 1), 1.0);
    $finalW = (int)($logoW * $scale);
    $finalH = (int)($logoH * $scale);
    if ($finalW < 1 || $finalH < 1) { imagedestroy($src); return false; }

    $margin = (int)min(30, $sw * 0.03, $sh * 0.03);
    $lx = max(0, min($margin, $sw - $finalW));
    $ly = max(0, min($margin, $sh - $finalH));
    $finalW = min($finalW, $sw - $lx);
    $finalH = min($finalH, $sh - $ly);
    if ($finalW < 1 || $finalH < 1) { imagedestroy($src); return false; }

    $logo = @imagecreatefrompng($logoPath);
    if (!$logo) { imagedestroy($src); return false; }
    $scaledLogo = imagecreatetruecolor($finalW, $finalH);
    imagealphablending($scaledLogo, false);
    imagesavealpha($scaledLogo, true);
    imagefill($scaledLogo, 0, 0, imagecolorallocatealpha($scaledLogo, 0, 0, 0, 127));
    imagecopyresampled($scaledLogo, $logo, 0, 0, 0, 0, $finalW, $finalH, $logoW, $logoH);
    imagedestroy($logo);

    $opacity = 0.8;
    for ($x = 0; $x < $finalW; $x++) {
        for ($y = 0; $y < $finalH; $y++) {
            $lp = imagecolorat($scaledLogo, $x, $y);
            $lAlpha = ($lp & 0x7F000000) >> 24;
            $op = (1 - $lAlpha / 127) * $opacity;
            if ($op < 0.01) continue;
            $sp = imagecolorat($src, $lx + $x, $ly + $y);
            $r = (int) round( ((($lp >> 16) & 0xFF) * $op) + ((($sp >> 16) & 0xFF) * (1 - $op)) );
            $g = (int) round( ((($lp >> 8) & 0xFF) * $op) + ((($sp >> 8) & 0xFF) * (1 - $op)) );
            $b = (int) round( ((($lp & 0xFF)) * $op) + ((($sp & 0xFF)) * (1 - $op)) );
            imagesetpixel($src, $lx + $x, $ly + $y, ($r << 16) | ($g << 8) | $b);
        }
    }
    imagedestroy($scaledLogo);

    $quality = ($ext === 'jpeg') ? 85 : 9;
    $tmp = $imagePath . '.wm_tmp';
    $result = @$dstFunc($src, $tmp, $quality);
    imagedestroy($src);
    if (!$result || !is_file($tmp)) {
        @unlink($tmp);
        error_log('Watermark: failed to save image');
        return false;
    }
    if (!@rename($tmp, $imagePath)) {
        if (!@copy($tmp, $imagePath) || !is_file($imagePath)) {
            @unlink($tmp);
            error_log('Watermark: failed to move processed image into place');
            return false;
        }
        @unlink($tmp);
    }
    return true;
}