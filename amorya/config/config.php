<?php
declare(strict_types=1);

define('APP_NAME', 'AMORYA');
define('BASE_URL', '/amorya'); // Si cambias la carpeta, actualiza esta ruta.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'amorya');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP normalmente usa contraseña vacía en instalaciones locales.

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
