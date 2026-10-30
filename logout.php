<?php
require_once __DIR__ . '/_lib.php';
session_start_safe();
$_SESSION = [];
session_destroy();
header('Location: /');
exit;
