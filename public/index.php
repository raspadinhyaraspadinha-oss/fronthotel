<?php
/**
 * Windsor Plaza — Payment Recovery System
 * Front controller (document root)
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// ── Bootstrap ──
// Hostinger: app/ lives at ../app (one level above public_html)
// Local dev: app/ lives at ../app (one level above public/)
define('APP_PATH', realpath(__DIR__ . '/../app') ?: __DIR__ . '/../app');
define('PUBLIC_PATH', __DIR__);

require_once APP_PATH . '/config/bootstrap.php';

// ── Routing ──
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET /admin'              => ['AdminController', 'dashboard'],
    'GET /admin/login'        => ['AdminController', 'loginForm'],
    'POST /admin/login'       => ['AdminController', 'loginSubmit'],
    'GET /admin/logout'       => ['AdminController', 'logout'],
    'POST /admin/upload'      => ['AdminController', 'upload'],
    'POST /admin/toggle-card' => ['AdminController', 'toggleCard'],
    'POST /admin/delete-all'  => ['AdminController', 'deleteAll'],
    'GET /admin/export'       => ['AdminController', 'export'],
    'POST /api/pix/create'    => ['PaymentController', 'pixCreate'],
    'GET /api/pix/status'     => ['PaymentController', 'pixStatus'],
    'POST /api/pix/webhook'   => ['PaymentController', 'pixWebhook'],
];

$routeKey = "$method $uri";

if (isset($routes[$routeKey])) {
    [$controller, $action] = $routes[$routeKey];
    require_once APP_PATH . "/src/controllers/$controller.php";
    $controller::$action();
    exit;
}

// /r/{CODE}
if (preg_match('#^/r/([A-Za-z0-9]{6})$#', $uri, $matches)) {
    require_once APP_PATH . '/src/controllers/ReservationController.php';
    ReservationController::show($matches[1]);
    exit;
}

if ($uri === '/') {
    header('Location: /admin');
    exit;
}

http_response_code(404);
require_once APP_PATH . '/src/views/public/not_found.php';
