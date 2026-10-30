# auth.wiredhowse.app

Central auth server for all wiredhowse/alphadropsupply/survivalskills.pro apps.

## Railway Environment Variables

| Variable | Value |
|---|---|
| `AUTH_SECRET` | Long random string — **same value in every app** |
| `AUTH_ADMIN_EMAIL` | Your login email |
| `AUTH_ADMIN_PASS` | Your login password (plaintext — hashed on seed) |
| `APP_URL` | `https://auth.wiredhowse.app` |
| `DB_HOST` | Railway MySQL host |
| `DB_NAME` | Railway MySQL database name |
| `DB_USER` | Railway MySQL user |
| `DB_PASS` | Railway MySQL password |

## Install

1. Set env vars in Railway console
2. Visit `https://auth.wiredhowse.app/install.php?token=<first-12-chars-of-AUTH_SECRET>`
3. Confirm admin seeded
4. Delete `install.php` from repo

## Integrating into any app

### 1. Add to your app's config

```php
define('AUTH_SECRET', 'same-secret-as-auth-server');
define('AUTH_SERVER', 'https://auth.wiredhowse.app');
define('JWT_TTL',     900);
```

### 2. Drop `_auth/` folder into your app root

Copy `_auth/check.php` and `_auth/logout.php` into your app.

### 3. Protect any page

```php
<?php
require_once __DIR__ . '/your-config.php';
require_once __DIR__ . '/_auth/check.php';
// $_SESSION['_auth_user']['email'] and ['role'] now available
?>
```

### 4. Logout link

```html
<a href="/_auth/logout.php">Sign out</a>
```

## Flow

```
User hits protected page
  → _auth/check.php: no session
  → redirect to auth.wiredhowse.app/?return=https://yourapp.com/page
  → user logs in
  → auth server issues JWT, redirects back with ?_auth_token=...
  → check.php validates JWT, sets local session, strips token from URL
  → page loads normally
  → subsequent requests: local session, auth server not hit again
```

## Allowed domains

Defined in `config.php` → `ALLOWED_DOMAINS`. Add new root domains there.
Currently: `wiredhowse.com`, `wiredhowse.app`, `alphadropsupply.com`, `survivalskills.pro`

## Adding users (future)

`users` table is ready. Add a registration flow to `index.php` or build `register.php` when needed.
