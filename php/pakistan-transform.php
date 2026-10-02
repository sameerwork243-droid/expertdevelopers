<?php
// pakistan-transform.php - one-off Pakistan rebrand transform (Expert Marketing & Developers).
// Runs on the live server using the app's own .env + PDO config. DELETE AFTER USE.
//   dry run : https://<host>/expertdevelopers/pakistan-transform.php?token=sX7-pak-rebrand-2026
//   apply   : ...&token=sX7-pak-rebrand-2026&commit=1

require_once __DIR__ . '/config/env.php';
env_load(__DIR__ . '/.env');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/pakistan.php';

header('Content-Type: text/plain; charset=utf-8');

if ((string) ($_GET['token'] ?? '') !== 'sX7-pak-rebrand-2026') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

$commit = isset($_GET['commit']) && (($_GET['commit'] === '1') || (strtolower((string) $_GET['commit']) === 'true'));
echo "mode: " . ($commit ? 'COMMIT' : 'DRY RUN (add &commit=1 to apply)') . "\n";
echo "db enabled: " . (db_enabled() ? 'yes' : 'no') . "\n\n";

const SKIP_KEYS = [
    'slug', 'id', 'ref', 'url', 'href', 'link', 'image', 'images', 'img', 'src',
    'video', 'file', 'files', 'icon', 'flag', 'path', 'phone', 'phones', 'email',
    'whatsapp', 'token', 'password', 'pass', 'secret', 'key', 'keys', 'currency',
    'price', 'rates', 'created_at', 'updated_at', 'created', 'updated', 'date',
    'dates', 'lat', 'lng', 'map', 'iframe',
];

const PAK_REPLACEMENTS = [
    'Dubai, United Arab Emirates' => 'Pakistan',
    'Dubai, UAE' => 'Pakistan',
    'United Arab Emirates' => 'Pakistan',
    'in the UAE' => 'in Pakistan',
    'the UAE' => 'Pakistan',
    'in Dubai' => 'in Pakistan',
    'UAE' => 'Pakistan',
    'Emirates' => 'Pakistan',
    'Dubai' => 'Pakistan',
];

function pak_plain_replace(string $v): string
{
    return str_ireplace(array_keys(PAK_REPLACEMENTS), array_values(PAK_REPLACEMENTS), $v);
}

function pak_string_filter(string $k, string $v): string
{
    $lk = strtolower((string) preg_replace('/[^a-z0-9]/i', '', $k));
    foreach (SKIP_KEYS as $skip) {
        if ($lk === $skip) return $v;
    }
    if (!preg_match('/Dubai|UAE|United Arab Emirates|Emirates/i', $v)) return $v;
    // Slug-style value (no spaces, dash/underscore separators) → area-name mapping
    if (!str_contains($v, ' ') && (str_contains($v, '-') || str_contains($v, '_'))) {
        return pak_area_name($v);
    }
    return pak_plain_replace($v);
}

function pak_json_filter(&$node, bool &$changed): void
{
    if (is_array($node)) {
        foreach ($node as $k => $v) {
            if (is_string($v)) {
                $n = pak_string_filter((string) $k, $v);
                if ($n !== $v) { $node[$k] = $n; $changed = true; }
            } elseif (is_array($v) || is_object($v)) {
                pak_json_filter($node[$k], $changed);
            }
        }
    } elseif (is_object($node)) {
        foreach ($node as $k => $v) {
            if (is_string($v)) {
                $n = pak_string_filter((string) $k, $v);
                if ($n !== $v) { $node->$k = $n; $changed = true; }
            } elseif (is_array($v) || is_object($v)) {
                pak_json_filter($node->$k, $changed);
            }
        }
    }
}

function trans_col(string $cell): ?string
{
    $decoded = json_decode($cell, true);
    if (is_array($decoded) || is_object($decoded)) {
        $changed = false;
        pak_json_filter($decoded, $changed);
        if (!$changed) return null;
        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return pak_string_filter('body', $cell) === $cell ? null : pak_string_filter('body', $cell);
}

function raw_json_dir(): string
{
    return rtrim(__DIR__ . '/data/raw', '/');
}

function process_raw_json(bool $commit): array
{
    $stats = ['files' => 0, 'changed' => 0, 'unchanged' => 0, 'skipped' => 0];
    if (!is_dir(raw_json_dir())) {
        echo "raw dir not found: " . raw_json_dir() . "\n";
        return $stats;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(raw_json_dir(), FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isDir() || strtolower($f->getExtension()) !== 'json') continue;
        $path = $f->getPathname();
        $stats['files']++;
        $raw = @file_get_contents($path);
        if ($raw === false) { $stats['skipped']++; continue; }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded) && !is_object($decoded)) { $stats['skipped']++; continue; }
        $changed = false;
        pak_json_filter($decoded, $changed);
        if (!$changed) { $stats['unchanged']++; continue; }
        $out = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($out === false || $out === null) { $stats['skipped']++; continue; }
        $stats['changed']++;
        if ($commit) {
            @file_put_contents($path, $out);
        } else {
            echo "JSON would change: " . str_replace(__DIR__ . '/', '', str_replace('\\', '/', $path)) . "\n";
        }
    }
    return $stats;
}

function process_db(bool $commit): array
{
    $stats = ['tables' => 0, 'cells' => 0, 'changes' => 0, 'deleted' => 0];
    if (!db_enabled()) {
        echo "DB not enabled on this server - skipping MySQL transform.\n";
        return $stats;
    }
    $pdo = db_connect();
    if (!$pdo) {
        echo "DB connect failed - skipping MySQL transform.\n";
        return $stats;
    }

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    // 1) text/JSON cells
    foreach ($tables as $t) {
        $cols = [];
        $pk = 'id';
        foreach ($pdo->query('DESCRIBE `' . $t . '`')->fetchAll() as $c) {
            $type = strtolower((string) $c['Type']);
            if ($c['Key'] === 'PRI') $pk = $c['Field'];
            if (str_contains($type, 'char') || str_contains($type, 'text') || str_contains($type, 'json') || str_contains($type, 'blob')) {
                $cols[] = $c['Field'];
            }
        }
        if (!$cols) continue;
        if (!in_array($pk, array_column($pdo->query('DESCRIBE `' . $t . '`')->fetchAll(), 'Field'), true)) {
            echo "SKIP (no primary key column): {$t}\n";
            continue;
        }
        $stats['tables']++;
        $sel = 'SELECT * FROM `' . $t . '`';
        $rows = $pdo->query($sel)->fetchAll();
        foreach ($rows as $i => $row) {
            foreach ($cols as $col) {
                $v = $row[$col] ?? null;
                if ($v === null || $v === '') continue;
                $nv = trans_col((string) $v);
                if ($nv === null) continue;
                $stats['changes']++;
                if ($commit) {
                    $st = $pdo->prepare('UPDATE `' . $t . '` SET `' . $col . '` = :nv WHERE `' . $pk . '` = :id');
                    $st->execute(['nv' => $nv, 'id' => ($row[$pk] ?? 0)]);
                } else {
                    if ($stats['changes'] <= 12) {
                        echo "DB would change: {$t}.{$col} {$pk}=" . ($row[$pk] ?? '?') . "\n  OLD: " .
                            substr(str_replace("\n", ' ', (string) $v), 0, 120) . "\n  NEW: " .
                            substr(str_replace("\n", ' ', $nv), 0, 120) . "\n";
                    }
                }
            }
        }
    }

    // 2) delete mortgage-named rows (slugs/links/urls only - never body text)
    foreach ($tables as $t) {
        $idCol = 'id';
        $keyCols = [];
        foreach ($pdo->query('DESCRIBE `' . $t . '`')->fetchAll() as $c) {
            if (strtolower((string) $c['Key']) === 'pri') $idCol = $c['Field'];
            $lc = strtolower((string) $c['Field']);
            if (in_array($lc, ['slug', 'link', 'url', 'href', 'path', 'route'], true)) $keyCols[] = $c['Field'];
        }
        if (!$keyCols) continue;
        foreach ($keyCols as $kc) {
            $st = $pdo->query('SELECT `' . $idCol . '`, `' . $kc . '` FROM `' . $t . '` WHERE `' . $kc . '` LIKE "%mortgage%"');
            $hits = $st ? $st->fetchAll() : [];
            foreach ($hits as $hit) {
                $stats['deleted']++;
                if ($commit) {
                    $pdo->prepare('DELETE FROM `' . $t . '` WHERE `' . $idCol . '` = :id')->execute(['id' => $hit[$idCol]]);
                } else {
                    echo "DB would DELETE: {$t}.{$kc} = " . $hit[$kc] . "\n";
                }
            }
        }
    }

    // 3) contact_info key/value settings -> Pakistan
    foreach ($tables as $t) {
        $c = [];
        $pk3 = 'id';
        foreach ($pdo->query('DESCRIBE `' . $t . '`')->fetchAll() as $x) {
            $c[] = strtolower((string) $x['Field']);
            if ($x['Key'] === 'PRI') $pk3 = $x['Field'];
        }
        if (!in_array('key', $c, true) && !in_array('contact_info', $c, true)) continue;
        if (!in_array($pk3, $c, true)) {
            echo "SKIP contact (no PK): {$t}\n";
            continue;
        }
        $rows = $pdo->query('SELECT * FROM `' . $t . '`')->fetchAll();
        foreach ($rows as $r) {
            $isContact = in_array('contact_info', $c, true) && isset($r['contact_info']);
            $isKeyVal = in_array('key', $c, true) && in_array('value', $c, true) && str_contains((string) ($r['key'] ?? ''), 'contact');
            if (!$isContact && !$isKeyVal) continue;
            $val = $isContact ? (string) $r['contact_info'] : (string) $r['value'];
            $dec = json_decode($val, true);
            if (!is_array($dec)) continue;
            $pk = [
                'phone' => '+92 312 5170194',
                'email' => $dec['email'] ?? 'buildersexpert42@gmail.com',
                'whatsapp' => 'https://wa.me/923125170194',
                'address' => 'Karachi, Pakistan',
            ];
            $out = json_encode($pk, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo "DB contact_info -> Pakistan (" . $t . ")\n";
            if ($commit) {
                if ($isContact) {
                    $pdo->prepare('UPDATE `' . $t . '` SET `contact_info` = :nv WHERE `' . $pk3 . '` = :id')->execute(['nv' => $out, 'id' => $r[$pk3]]);
                } else {
                    $pdo->prepare('UPDATE `' . $t . '` SET `value` = :nv WHERE `' . $pk3 . '` = :id')->execute(['nv' => $out, 'id' => $r[$pk3]]);
                }
            }
        }
    }

    // 4) price_qualifier AED -> PKR
    foreach ($tables as $t) {
        $has = false;
        foreach ($pdo->query('DESCRIBE `' . $t . '`')->fetchAll() as $x) {
            if (strtolower((string) $x['Field']) === 'price_qualifier') $has = true;
        }
        if (!$has) continue;
        echo "price_qualifier AED -> PKR on " . $t . "\n";
        if ($commit) {
            $pdo->exec('UPDATE `' . $t . '` SET `price_qualifier` = "PKR" WHERE `price_qualifier` = "AED"');
        }
    }

    return $stats;
}

$d = process_db($commit);
$r = process_raw_json($commit);
echo "\n=== SUMMARY ===\n";
echo "DB tables scanned: {$d['tables']}\nDB cells changed:   {$d['changes']}\nDB rows deleted:    {$d['deleted']}\n";
echo "JSON files: {$r['files']} total, {$r['changed']} changed, {$r['unchanged']} unchanged, {$r['skipped']} skipped\n";
echo ($commit ? "COMMIT finished.\n" : "Dry run only. Re-run with &commit=1 to apply.\n");
echo "REMEMBER: delete this file from the server.\n";