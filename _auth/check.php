<?php
/**
 * _auth/check.php
 * Drop into any app. Include at the top of any protected page.
 *
 * Requires in your app's config (or define before including):
 *   define('AUTH_SECRET', 'same-secret-as-auth-server');
 *   define('AUTH_SERVER', 'https://auth.wiredhowse.app');
 *   define('JWT_TTL', 900); // must match auth server
 */

if (!defined('AUTH_SECRET') || !defined('AUTH_SERVER')) {
    die('_auth/check.php: AUTH_SECRET and AUTH_SERVER must be defined.');
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Already have a valid local session
if (!empty($_SESSION['_auth_user'])) {
    return;
}

// Incoming token from auth server redirect
if (!empty($_GET['_auth_token'])) {
    $token  = $_GET['_auth_token'];
    $parts  = explode('.', $token);

    if (count($parts) === 3) {
        [$header, $body, $sig] = $parts;
        $expected = _auth_base64url(hash_hmac('sha256', "$header.$body", AUTH_SECRET, true));

        if (hash_equals($expected, $sig)) {
            $payload = json_decode(base64_decode(strtr($body, '-_', '+/')), true);

            if ($payload && isset($payload['exp']) && $payload['exp'] >= time()) {
                $_SESSION['_auth_user'] = [
                    'email' => $payload['email'],
                    'role'  => $payload['role'] ?? 'user',
                ];
                session_regenerate_id(true);

                // Strip token from URL and redirect clean
                $clean = strtok($_SERVER['REQUEST_URI'], '?');
                $params = $_GET;
                unset($params['_auth_token']);
                if ($params) $clean .= '?' . http_build_query($params);

                header('Location: ' . $clean);
                exit;
            }
        }
    }

    // Bad token — fall through to login redirect
}

// Not authenticated — send to auth server
$return = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
header('Location: ' . AUTH_SERVER . '/?return=' . urlencode($return));
exit;

function _auth_base64url(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
