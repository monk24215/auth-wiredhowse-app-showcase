<?php
require_once __DIR__ . '/config.php';

// --- PDO ---
function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

// --- JWT (HS256, no library) ---
function jwt_issue(array $payload): string {
    $header  = base64url(json_encode(['alg'=>'HS256','typ'=>'JWT']));
    $payload['exp'] = time() + JWT_TTL;
    $body    = base64url(json_encode($payload));
    $sig     = base64url(hash_hmac('sha256', "$header.$body", AUTH_SECRET, true));
    return "$header.$body.$sig";
}

function jwt_verify(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $body, $sig] = $parts;
    $expected = base64url(hash_hmac('sha256', "$header.$body", AUTH_SECRET, true));
    if (!hash_equals($expected, $sig)) return null;
    $payload = json_decode(base64_decode(strtr($body, '-_', '+/')), true);
    if (!$payload || $payload['exp'] < time()) return null;
    return $payload;
}

function base64url(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// --- Return URL validation ---
function valid_return(string $url): bool {
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return false;
    foreach (ALLOWED_DOMAINS as $domain) {
        if ($host === $domain || str_ends_with($host, '.' . $domain)) return true;
    }
    return false;
}

// --- Password ---
function verify_password(string $plain, string $hash): bool {
    return password_verify($plain, $hash);
}

function hash_password(string $plain): string {
    return password_hash($plain, PASSWORD_BCRYPT);
}

// --- Session bootstrap ---
function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

// --- Logged-in check (for auth server itself) ---
function auth_user(): ?array {
    session_start_safe();
    return $_SESSION['auth_user'] ?? null;
}
