<?php
/**
 * Bootstrap — loads env, database, models, helpers
 */

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../src/models/Reservation.php';
require_once __DIR__ . '/../src/models/Setting.php';

// Ensure database exists on first run
getDatabase();

// ── CSRF Helper ──
function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . csrfToken() . '">';
}

function verifyCsrf(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function requireCsrf(): void
{
    if (!verifyCsrf()) {
        http_response_code(403);
        die('Sessão expirada. <a href="javascript:history.back()">Voltar</a>');
    }
}

// ── Helpers ──
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function formatBRL(float $value): string
{
    return number_format($value, 2, ',', '.');
}
