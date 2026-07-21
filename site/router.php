<?php
declare(strict_types=1);

/**
 * Роутер для встроенного сервера PHP:
 * php -S localhost:8000 router.php
 *
 * Из каталога site/:
 * php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$file = __DIR__ . $uri;

if ($uri !== '/' && is_file($file)) {
    return false;
}

if (is_dir($file)) {
    $index = rtrim($file, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.php';
    if (is_file($index)) {
        require $index;
        return true;
    }
}

http_response_code(404);
require __DIR__ . '/404.php';
return true;
