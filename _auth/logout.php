<?php
/**
 * _auth/logout.php
 * Clears local session and optionally redirects to auth server logout.
 * Hit this URL in any app to log out locally.
 */

if (!defined('AUTH_SERVER')) {
    define('AUTH_SERVER', 'https://auth.wiredhowse.app');
}

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION = [];
session_destroy();

// Redirect to auth server so SSO session is also cleared
header('Location: ' . AUTH_SERVER . '/logout.php');
exit;
