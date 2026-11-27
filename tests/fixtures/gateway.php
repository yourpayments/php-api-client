<?php

declare(strict_types=1);

/**
 * Заглушка шлюза для тестов: запускается встроенным сервером PHP
 * и возвращает полученный запрос обратно в виде JSON.
 */

$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/slow') {
    sleep(3);
}

if ($path === '/error500') {
    http_response_code(500);
}

if ($path === '/html') {
    http_response_code(502);
    header('Content-Type: text/html');
    echo '<h1>Bad Gateway</h1>';

    return;
}

header('Content-Type: application/json');
header('X-Test-Response: yes');

if ($path === '/token') {
    header('Set-Cookie: session=server-secret');
    echo json_encode(['token' => 'tok_secret_value', 'cardDetails' => ['pan' => '5555555555554444', 'type' => 'MC']]);

    return;
}

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'headers' => array_change_key_case(getallheaders(), CASE_LOWER),
    'body' => file_get_contents('php://input'),
]);
