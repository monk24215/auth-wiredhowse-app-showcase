<?php
require_once __DIR__ . '/_lib.php';
session_start_safe();
if (!auth_user()) { header('Location: /'); exit; }
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Auth Dashboard</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #0f0f0f; color: #e0e0e0; font-family: system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 8px; padding: 2.5rem; width: 100%; max-width: 420px; }
h1 { font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: .5rem; }
p { color: #888; font-size: .9rem; margin-bottom: 1.5rem; }
a { color: #2a6ef5; font-size: .9rem; }
.badge { display: inline-block; background: #1e3a6e; color: #93c5fd; border-radius: 4px; font-size: .75rem; padding: .2rem .5rem; margin-left: .5rem; vertical-align: middle; }
</style>
</head>
<body>
<div class="card">
    <h1><?= htmlspecialchars($user['email']) ?> <span class="badge"><?= htmlspecialchars($user['role']) ?></span></h1>
    <p>You are authenticated.</p>
    <a href="/logout.php">Sign out</a>
</div>
</body>
</html>
