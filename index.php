<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/actions.php';

$files = glob($dataDir . '*.txt') ?: [];
$editFile = isset($_GET['edit']) ? basename($_GET['edit']) : '';
$editData = $editFile ? loadProgram($dataDir . $editFile) : null;

$filter = $_GET['filter'] ?? 'all';
$sort   = $_GET['sort']   ?? 'alpha';
$letter = strtoupper($_GET['letter'] ?? 'all');

$programs = [];
$availableLetters = [];

foreach ($files as $f) {
    $data = loadProgram($f);
    if (!$data) continue;
    $data['_file'] = basename($f);

	// 1. Filter by status / favourite first
    if ($filter === 'fav' && empty($data['is_favourite'])) continue;
    if ($filter === 'active' && empty($data['is_active'])) continue;
    if ($filter === 'inactive' && !empty($data['is_active'])) continue;

    // 2. Determine the first character of the program name (0-9 or A-Z)
    $firstChar = mb_strtoupper(mb_substr(trim($data['program_name'] ?? ''), 0, 1, 'UTF-8'), 'UTF-8');
    if (preg_match('/[0-9]/', $firstChar)) {
        $charGroup = '0-9';
    } elseif (preg_match('/[A-Z]/', $firstChar)) {
        $charGroup = $firstChar;
    } else {
        $charGroup = '#';
    }
    $availableLetters[$charGroup] = true;

    // 3. Filter by selected letter/number
    if ($letter !== 'ALL' && $charGroup !== $letter) continue;

    $programs[] = $data;
}


usort($programs, function($a, $b) use ($sort) {
    if ($sort === 'earnings') {
        $diff = (float)($b['earnings'] ?? 0) <=> (float)($a['earnings'] ?? 0);
        return $diff !== 0 ? $diff : strnatcasecmp($a['program_name'] ?? '', $b['program_name'] ?? '');
    }
    return strnatcasecmp($a['program_name'] ?? '', $b['program_name'] ?? '');
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Local Affiliate Manager</title>
	<link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>

<?php
	$showIgnore = isset($_GET['ignore']);
	$ignoreFile = __DIR__ . '/ignore_list.txt';
	$ignoreContent = file_exists($ignoreFile) ? file_get_contents($ignoreFile) : '';
?>

<div class="topbar">
    <h1><a href="index.php" style="text-decoration: none; color: inherit;">PHP Local Affiliate Vault</a></h1>
    <div style="display: flex; gap: 8px; align-items: center;">
        <a href="<?= $showIgnore ? 'index.php' : '?ignore=1' ?>" class="btn <?= $showIgnore ? 'btn-active' : 'btn-secondary' ?>">
            🚫 Ignore List
        </a>
        <a href="?export=1" class="btn btn-secondary">Download Backup (.json)</a>
    </div>
</div>

<?php if ($showIgnore): ?>
    <div class="box" style="border-color: #dc2626;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h2 style="margin: 0;">Ignore List (Programs to Avoid)</h2>
            <a href="index.php" class="btn btn-secondary btn-sm">✕ Close</a>
        </div>
        <p style="color: #4b5563; font-size: 13px; margin-top: 0;">
            Enter affiliate programs or casinos you want to avoid (one per line, optionally with a short note on why).
        </p>

        <?php if (isset($_GET['saved'])): ?>
            <div style="background: #dcfce7; color: #166534; border: 1px solid #22c55e; padding: 8px 12px; border-radius: 4px; margin-bottom: 12px; font-size: 13px; font-weight: bold;">
                ✓ Ignore list saved!
            </div>
        <?php endif; ?>

        <form method="POST">
            <textarea name="ignore_list_content" style="height: 360px; font-family: monospace; font-size: 14px; line-height: 1.5;" placeholder="Example:&#10;Noname Partners - Rogue / locks inactive accounts&#10;ExamplePartners - Poor terms / negative carryover"><?= h($ignoreContent) ?></textarea>
            <div style="display: flex; gap: 8px;">
                <button type="submit" name="save_ignore_list">Save Ignore List</button>
                <a href="index.php" class="btn btn-secondary">Back to Manager</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <?php require __DIR__ . '/forms.php'; ?>
    <?php require __DIR__ . '/list.php'; ?>
<?php endif; ?>


<script>
function togglePw(id, btn) {
    const el = document.getElementById(id);
    el.type = (el.type === 'password') ? 'text' : 'password';
    btn.textContent = (el.type === 'password') ? 'Show' : 'Hide';
}

function copyUrl(btn) {
    const input = btn.parentElement.querySelector('input[name="affiliate_url"]');
    if (!input) return;

    navigator.clipboard.writeText(input.value).then(() => {
        const origText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.add('btn-copied');
        setTimeout(() => {
            btn.textContent = origText;
            btn.classList.remove('btn-copied');
        }, 1500);
    }).catch(() => {
        input.select();
        document.execCommand('copy');
    });
}
</script>
</body>
</html>
