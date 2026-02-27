<?php
/**
 * Windsor Plaza — Payment Recovery System
 *
 * Estrutura na Hostinger (public_html):
 *   public_html/
 *   ├── index.php       ← este arquivo
 *   ├── .htaccess
 *   ├── check.php       ← diagnóstico (apague após usar)
 *   ├── assets/
 *   └── app/
 *       ├── .env
 *       ├── config/
 *       ├── src/
 *       └── storage/
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('APP_PATH', __DIR__ . '/app');
define('PUBLIC_PATH', __DIR__);

// ── Verifica se app/ existe antes de tentar carregar ──
if (!file_exists(APP_PATH . '/config/bootstrap.php')) {
    http_response_code(500);
    die(
        '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Erro de configuração</title>' .
        '<style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#faf6ef}' .
        '.box{background:#fff;padding:40px;border-radius:16px;max-width:500px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.08)}' .
        'h1{color:#c9a96e;font-size:20px;margin-bottom:12px}p{color:#666;font-size:14px;line-height:1.7}code{background:#f0f0f0;padding:2px 6px;border-radius:4px}</style>' .
        '</head><body><div class="box"><h1>Pasta app/ não encontrada</h1>' .
        '<p>Certifique-se de ter enviado a pasta <code>app/</code> para dentro de <code>public_html/</code>.<br><br>' .
        'Acesse <a href="/check.php">check.php</a> para diagnóstico completo.</p></div></body></html>'
    );
}

// ── Bootstrap com captura de erros ──
try {
    require_once APP_PATH . '/config/bootstrap.php';
} catch (Throwable $e) {
    http_response_code(500);
    $debug = file_exists(__DIR__ . '/check.php');
    $msg = $debug ? htmlspecialchars($e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine()) : 'Erro de configuração.';
    die(
        '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Erro</title>' .
        '<style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#faf6ef}' .
        '.box{background:#fff;padding:40px;border-radius:16px;max-width:640px;box-shadow:0 4px 24px rgba(0,0,0,.08)}' .
        'h1{color:#c9a96e;font-size:18px;margin-bottom:12px}pre{background:#fff0f0;padding:14px;border-radius:8px;font-size:12px;overflow:auto;color:#c0392b}' .
        'p{color:#666;font-size:14px}</style></head><body><div class="box"><h1>Erro ao inicializar</h1>' .
        '<pre>' . $msg . '</pre><p>Acesse <a href="/check.php">check.php</a> para diagnóstico.</p></div></body></html>'
    );
}

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
    require_once APP_PATH . "/src/controllers/{$controller}.php";
    try {
        $controller::$action();
    } catch (Throwable $e) {
        http_response_code(500);
        $debug = file_exists(__DIR__ . '/check.php');
        die($debug
            ? '<pre style="padding:20px;font-family:monospace;background:#fff0f0;color:#c0392b">' . htmlspecialchars($e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine()) . '</pre>'
            : 'Erro interno. Contate o administrador.');
    }
    exit;
}

// /r/{CODE}
if (preg_match('#^/r/([A-Za-z0-9]{6})$#', $uri, $matches)) {
    require_once APP_PATH . '/src/controllers/ReservationController.php';
    try {
        ReservationController::show($matches[1]);
    } catch (Throwable $e) {
        http_response_code(500);
        $debug = file_exists(__DIR__ . '/check.php');
        die($debug
            ? '<pre style="padding:20px;font-family:monospace;background:#fff0f0;color:#c0392b">' . htmlspecialchars($e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine()) . '</pre>'
            : 'Erro interno. Contate o administrador.');
    }
    exit;
}

if ($uri === '/') {
    header('Location: /admin');
    exit;
}

http_response_code(404);
require_once APP_PATH . '/src/views/public/not_found.php';
