<?php
// head.php — <head> block (port of src/app/layout.tsx metadata)
// Expects: $page_title (string), $page_description (string), $canonical (string, optional),
//          $og_image (string, optional), $body_class (string, optional)
// Global variables used by pages. Call render_head() inside <head>.

require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? '';
$page_description = $page_description ?? 'Your one-stop for all real estate services, including selling, renting, snagging, conveyancing, property management, & expert property consultants.';
$canonical = $canonical ?? '';
$og_image = $og_image ?? site_base_url() . '/icons/icon-512x512.png';

function render_head(): void
{
    global $page_title, $page_description, $canonical, $og_image;

    $title = $page_title !== ''
        ? $page_title . ' | Expert Marketing & Developers'
        : 'Leading Real Estate Agency in Pakistan | Expert Marketing & Developers';

    $canonical = $canonical !== '' ? $canonical : site_base_url() . ($_SERVER['REQUEST_URI'] ?? '/');
    $title = pak_replace($title);
    $page_description = pak_replace($page_description);
    ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?></title>
<meta name="description" content="<?= esc($page_description) ?>">
<meta name="robots" content="index, follow">
<link rel="icon" type="image/png" href="/images/expertlogo.png">
<link rel="canonical" href="<?= esc($canonical) ?>">
<meta property="og:title" content="Leading Real Estate Agency in Pakistan">
<meta property="og:site_name" content="Expert Marketing & Developers">
<meta property="og:image" content="<?= esc($og_image) ?>">
<meta property="og:type" content="website">
<link rel="stylesheet" href="/assets/css/expert.css?v=<?= APP_ASSET_VER ?>">
<link rel="stylesheet" href="/assets/css/header-styles.css?v=<?= APP_ASSET_VER ?>">
<link rel="stylesheet" href="/assets/css/developer-styles-v5.css?v=<?= APP_ASSET_VER ?>">
<link rel="stylesheet" href="/assets/css/app-styles.css?v=<?= APP_ASSET_VER ?>">
<link rel="stylesheet" href="/assets/css/portal.css?v=<?= APP_ASSET_VER ?>">
<link rel="stylesheet" href="/assets/css/app-shell.css?v=<?= APP_ASSET_VER ?>">
<?php
}

function render_site_footer_scripts(): void
{
    ?>
<script>window.EXPERT_RATES = <?= json_encode(expert_rates(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;window.EXPERT_BASE = <?= json_encode(app_base_path()) ?>;</script>
<script src="/assets/js/main.js?v=<?= APP_ASSET_VER ?>" defer></script>
<script src="/assets/js/site-prefs.js?v=<?= APP_ASSET_VER ?>" defer></script>
<script src="/assets/js/listing-ui.js?v=<?= APP_ASSET_VER ?>" defer></script>
<script src="/assets/js/property.js?v=<?= APP_ASSET_VER ?>" defer></script>
<script src="/assets/js/content-ui-v4.js?v=<?= APP_ASSET_VER ?>" defer></script>
    <?php
}