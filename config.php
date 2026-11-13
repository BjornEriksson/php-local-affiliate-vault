<?php
$dataDir = __DIR__ . '/data/';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function loadProgram($filePath) {
    if (!file_exists($filePath)) return null;
    return json_decode(file_get_contents($filePath), true);
}
