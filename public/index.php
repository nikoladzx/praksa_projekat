<?php

declare(strict_types=1);

session_start();

$container = require_once __DIR__ . '/../Bootstrap.php';

require_once __DIR__ . '/../src/Routing/Routes.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routePath = parse_url($requestUri, PHP_URL_PATH);

header('Content-Type: application/json');
$result = $router->handleRequest($requestMethod, $routePath);
echo json_encode($result);