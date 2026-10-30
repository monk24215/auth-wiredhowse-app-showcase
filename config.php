<?php
define('AUTH_SECRET',   getenv('AUTH_SECRET')   ?: '7f3a9d2e1b8c4f6a0e5d7c3b9a2f1e8d4c6b0a7f3e9d2c1b8a5f4e7d3c');
define('AUTH_URL',      getenv('APP_URL')        ?: 'https://auth.wiredhowse.app');
define('ADMIN_EMAIL',   getenv('AUTH_ADMIN_EMAIL') ?: 'koubre@gmail.com');
define('ADMIN_PASS',    getenv('AUTH_ADMIN_PASS')  ?: 'Wh0use2025!#Secure');
define('DB_HOST',       getenv('DB_HOST')        ?: 'mysql.railway.internal');
define('DB_NAME',       getenv('DB_NAME')        ?: 'railway');
define('DB_USER',       getenv('DB_USER')        ?: 'root');
define('DB_PASS',       getenv('DB_PASS')        ?: '');
define('JWT_TTL',       900); // 15 minutes
define('SESSION_TTL',   86400 * 30); // 30 days

define('ALLOWED_DOMAINS', [
    'wiredhowse.com',
    'wiredhowse.app',
    'alphadropsupply.com',
    'survivalskills.pro',
    'up.railway.app',
]);
