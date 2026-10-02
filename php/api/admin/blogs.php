<?php
// api/admin/blogs.php — CRUD over blog posts stored as pages/blog/{slug}.json
// files (the same source the public /blog/ pages render). Supports the generic
// admin table UI: GET list/search, POST create, PUT update, DELETE remove.
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

const ADMIN_BLOG_DIR = APP_RAW_DIR . '/pages/blog';

function blog_file_id(string $slug): int
{
    return crc32(strtolower($slug)) & 0x7fffffff;
}

function blog_read_all(): array
{
    $items = [];
    foreach (glob(ADMIN_BLOG_DIR . '/*.json') ?: [] as $f) {
        $raw = @file_get_contents($f);
        $j = $raw !== false ? json_decode($raw, true) : null;
        $b = is_array($j) ? ($j['result']['data']['strapiBlog'] ?? null) : null;
        if (!is_array($b)) continue;
        $cat = $b['category']['strapi_json_value'] ?? null;
        if (is_array($cat) && is_string($cat[0] ?? null)) {
            $category = implode(', ', $cat);
        } else {
            $category = (string) ($b['category'] ?? '');
        }
        $items[] = [
            'id' => blog_file_id((string) ($b['slug'] ?? '')),
            '_file' => $f,
            'slug' => (string) ($b['slug'] ?? ''),
            'title' => (string) ($b['title'] ?? ''),
            'category' => $category,
            'author' => (string) ($b['author'] ?? ''),
            'date' => (string) ($b['date'] ?? ''),
            'published' => empty($b['publish']) ? 0 : 1,
            'short_description' => (string) ($b['short_description'] ?? ''),
            'banner_image' => is_array($b['banner_image'] ?? null) ? (string) ($b['banner_image']['url'] ?? '') : '',
            'content' => '',
        ];
    }
    usort($items, fn ($a, $b) => ((int) $b['id']) <=> ((int) $a['id']));
    return $items;
}

function blog_write_file(string $file, array $d, string $slug): void
{
    $now = gmdate('Y-m-d\TH:i:s.v\Z');
    $content = (string) ($d['content'] ?? '');
    $banner = trim((string) ($d['banner_image'] ?? ''));
    $short = trim((string) ($d['short_description'] ?? ''));
    if ($short === '') $short = 'Latest news and insights from Expert Marketing & Developers.';
    $b = [
        'id' => blog_file_id($slug),
        'strapi_id' => blog_file_id($slug),
        'title' => (string) ($d['title'] ?? ''),
        'slug' => $slug,
        'author' => (string) ($d['author'] ?? ''),
        'date' => (string) ($d['date'] ?? date('Y-m-d')),
        'short_description' => $short,
        'category' => ['strapi_json_value' => [trim((string) ($d['category'] ?? 'News')) ?: 'News']],
        'publish' => !empty($d['published']),
        'video_url' => null,
        'banner_image' => $banner !== '' ? ['url' => $banner] : null,
        'tile_image' => ['url' => $banner !== '' ? $banner : '/images/expertlogo.png'],
        'seo' => [],
        'createdAt' => $now,
        'updatedAt' => $now,
        'modules' => [[
            'strapi_component' => 'components.rich-text-block',
            'text' => ['data' => ['text' => $content]],
        ]],
    ];
    $payload = [
        'componentChunkName' => 'component---blog-detail-tsx',
        'path' => '/blog/' . $slug . '/',
        'result' => ['data' => ['strapiBlog' => $b]],
        'staticQueryHashes' => [],
    ];
    @mkdir(dirname($file), 0755, true);
    file_put_contents($file, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function blog_slugify(string $title): string
{
    $slug = preg_replace('/-+/', '-', trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-'));
    return $slug !== '' ? $slug : 'post-' . time();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $q = trim((string) ($_GET['q'] ?? ''));
    $items = blog_read_all();
    if ($q !== '') {
        $items = array_values(array_filter($items, function ($it) use ($q) {
            return stripos($it['title'], $q) !== false
                || stripos($it['slug'], $q) !== false
                || stripos($it['category'], $q) !== false
                || stripos($it['author'], $q) !== false;
        }));
    }
    json_response(['items' => $items, 'total' => count($items)]);
}

$body = json_body();

if ($method === 'POST') {
    $title = trim((string) ($body['title'] ?? ''));
    if ($title === '') json_response(['error' => 'Missing title'], 400);
    $slug = trim((string) ($body['slug'] ?? ''));
    if ($slug === '') $slug = blog_slugify($title);
    $file = ADMIN_BLOG_DIR . '/' . $slug . '.json';
    if (file_exists($file)) json_response(['error' => 'A post with this slug already exists'], 409);
    blog_write_file($file, $body, $slug);
    json_response(['ok' => true, 'id' => blog_file_id($slug)], 201);
}

$id = (int) ($_GET['id'] ?? 0);
if (!$id) json_response(['error' => 'Missing id'], 400);

$targetFile = null;
foreach (blog_read_all() as $it) {
    if ((int) $it['id'] === $id) { $targetFile = $it['_file']; break; }
}
if (!$targetFile) json_response(['error' => 'Not found'], 404);

if ($method === 'PUT') {
    $slug = trim((string) ($body['slug'] ?? ''));
    $newFile = $slug === '' ? null : (ADMIN_BLOG_DIR . '/' . $slug . '.json');
    if ($newFile !== null && $newFile !== $targetFile && file_exists($newFile)) {
        json_response(['error' => 'A post with this slug already exists'], 409);
    }
    blog_write_file($newFile ?? $targetFile, $body, $slug !== '' ? $slug : basename($targetFile, '.json'));
    if ($newFile !== null && $newFile !== $targetFile) @unlink($targetFile);
    json_response(['ok' => true]);
}

if ($method === 'DELETE') {
    @unlink($targetFile);
    json_response(['ok' => true]);
}

json_response(['error' => 'Method not allowed'], 405);