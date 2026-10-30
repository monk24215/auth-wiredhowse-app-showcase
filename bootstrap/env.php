<?php
$envFile = dirname(__DIR__) . '/.env';
if (!file_exists($envFile)) return;
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if ($line[0] === '#') continue;
    [$k,$v] = array_map('trim', explode('=', $line, 2));
    $_ENV[$k] = $v;
}
