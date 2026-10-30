<?php
require_once __DIR__ . '/_lib.php';
session_start_safe();

$return = $_GET['return'] ?? $_POST['return'] ?? '';
$error  = '';

// Already logged in — issue JWT and redirect
if (auth_user() && $return) {
    if (!valid_return($return)) { http_response_code(400); die('Invalid return URL.'); }
    $jwt = jwt_issue(['email' => $_SESSION['auth_user']['email'], 'role' => $_SESSION['auth_user']['role']]);
    $sep = str_contains($return, '?') ? '&' : '?';
    header('Location: ' . $return . $sep . '_auth_token=' . urlencode($jwt));
    exit;
}

if (auth_user() && !$return) {
    // Logged in, no return — show dashboard
    header('Location: /dashboard.php');
    exit;
}

// POST — process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        try {
            $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND active = 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && verify_password($pass, $user['password_hash'])) {
                $_SESSION['auth_user'] = ['id' => $user['id'], 'email' => $user['email'], 'role' => $user['role']];
                session_regenerate_id(true);

                if ($return && valid_return($return)) {
                    $jwt = jwt_issue(['email' => $user['email'], 'role' => $user['role']]);
                    $sep = str_contains($return, '?') ? '&' : '?';
                    header('Location: ' . $return . $sep . '_auth_token=' . urlencode($jwt));
                } else {
                    header('Location: /dashboard.php');
                }
                exit;
            }
        } catch (Exception $e) { /* fall through */ }
        $error = 'Invalid email or password.';
    } else {
        $error = 'Email and password required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign In</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #0f0f0f; color: #e0e0e0; font-family: system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 8px; padding: 2.5rem; width: 100%; max-width: 380px; }
h1 { font-size: 1.25rem; font-weight: 600; margin-bottom: 2rem; color: #fff; }
label { display: block; font-size: .8rem; color: #999; margin-bottom: .4rem; }
input { width: 100%; background: #111; border: 1px solid #333; border-radius: 5px; color: #e0e0e0; font-size: .95rem; padding: .6rem .8rem; margin-bottom: 1.2rem; outline: none; }
input:focus { border-color: #555; }
button { width: 100%; background: #2a6ef5; border: none; border-radius: 5px; color: #fff; font-size: .95rem; font-weight: 600; padding: .7rem; cursor: pointer; }
button:hover { background: #1a5ee0; }
.error { background: #3a1a1a; border: 1px solid #7a2a2a; border-radius: 5px; color: #f87171; font-size: .85rem; margin-bottom: 1.2rem; padding: .6rem .8rem; }
.brand { color: #555; font-size: .75rem; margin-top: 1.5rem; text-align: center; }
</style>
</head>
<body>
<div class="card">
    <h1>Sign in</h1>
    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="return" value="<?= htmlspecialchars($return) ?>">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" autocomplete="email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
        <button type="submit">Sign in</button>
    </form>
    <p class="brand">wiredhowse auth</p>
</div>
</body>
</html>
