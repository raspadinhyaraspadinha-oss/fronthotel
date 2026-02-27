<?php
/**
 * ════════════════════════════════════════
 *  Windsor Plaza — Diagnóstico do Servidor
 *  Acesse: seusite.com/check.php
 *
 *  ⚠️ APAGUE ESTE ARQUIVO após resolver!
 * ════════════════════════════════════════
 */

// Mostra todos os erros para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', '1');

$APP = __DIR__ . '/app';

// ── Testa tudo ──
$checks = [];

// 1. Versão do PHP
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
$checks[] = [
    'label' => 'Versão PHP',
    'value' => $phpVersion,
    'ok'    => $phpOk,
    'fix'   => 'Vá em hPanel → Avançado → Configuração PHP e selecione PHP 8.1 ou 8.2',
];

// 2. Extensão PDO
$pdoOk = extension_loaded('pdo');
$checks[] = ['label' => 'Extensão PDO', 'value' => $pdoOk ? 'Disponível' : 'AUSENTE', 'ok' => $pdoOk, 'fix' => 'Ative PDO em hPanel → PHP → Extensões'];

// 3. PDO SQLite
$pdoSqliteOk = extension_loaded('pdo_sqlite');
$checks[] = ['label' => 'PDO SQLite', 'value' => $pdoSqliteOk ? 'Disponível' : 'AUSENTE', 'ok' => $pdoSqliteOk, 'fix' => 'Ative pdo_sqlite em hPanel → PHP → Extensões'];

// 4. cURL
$curlOk = extension_loaded('curl');
$checks[] = ['label' => 'Extensão cURL', 'value' => $curlOk ? 'Disponível' : 'AUSENTE', 'ok' => $curlOk, 'fix' => 'Ative curl em hPanel → PHP → Extensões'];

// 5. mbstring
$mbOk = extension_loaded('mbstring');
$checks[] = ['label' => 'Extensão mbstring', 'value' => $mbOk ? 'Disponível' : 'AUSENTE', 'ok' => $mbOk, 'fix' => 'Ative mbstring em hPanel → PHP → Extensões'];

// 6. Pasta app/ existe
$appExists = is_dir($APP);
$checks[] = ['label' => 'Pasta app/', 'value' => $appExists ? "Encontrada em $APP" : "NÃO encontrada em $APP", 'ok' => $appExists, 'fix' => 'Suba a pasta app/ para dentro de public_html/'];

// 7. .env existe
$envPath = $APP . '/.env';
$envExists = file_exists($envPath);
$checks[] = ['label' => 'Arquivo app/.env', 'value' => $envExists ? 'Encontrado' : "NÃO encontrado em $envPath", 'ok' => $envExists, 'fix' => 'Suba o arquivo .env para dentro de public_html/app/'];

// 8. bootstrap.php existe
$bootExists = file_exists($APP . '/config/bootstrap.php');
$checks[] = ['label' => 'app/config/bootstrap.php', 'value' => $bootExists ? 'Encontrado' : 'NÃO encontrado', 'ok' => $bootExists, 'fix' => 'Suba a pasta app/config/ corretamente'];

// 9. Pasta storage/ existe e tem permissão de escrita
$storageDir = $APP . '/storage';
$storageExists = is_dir($storageDir);
$storageWritable = $storageExists && is_writable($storageDir);
if (!$storageExists) {
    @mkdir($storageDir, 0755, true);
    $storageExists = is_dir($storageDir);
    $storageWritable = $storageExists && is_writable($storageDir);
}
$checks[] = ['label' => 'app/storage/ (escrita)', 'value' => $storageWritable ? 'OK — escrita permitida' : ($storageExists ? 'Pasta existe mas SEM permissão de escrita' : 'Pasta NÃO existe'), 'ok' => $storageWritable, 'fix' => 'Crie a pasta app/storage/ e defina permissão 755 no Gerenciador de Arquivos'];

// 10. Testa escrita de arquivo
$testFile = $storageDir . '/.write_test';
$writeOk = false;
if ($storageWritable) {
    $writeOk = file_put_contents($testFile, 'ok') !== false;
    @unlink($testFile);
}
$checks[] = ['label' => 'Escrita em app/storage/', 'value' => $writeOk ? 'Funcionando' : 'FALHOU', 'ok' => $writeOk, 'fix' => 'Defina permissão 755 na pasta app/storage/'];

// 11. Testa criação do SQLite
$dbOk = false;
$dbMsg = '';
if ($pdoSqliteOk && $storageWritable) {
    try {
        $dbPath = $storageDir . '/database.sqlite';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->exec('CREATE TABLE IF NOT EXISTS _test (id INTEGER)');
        $pdo->exec('DROP TABLE _test');
        $dbOk = true;
        $dbMsg = "SQLite OK — $dbPath";
    } catch (Exception $e) {
        $dbMsg = 'Erro: ' . $e->getMessage();
    }
}
$checks[] = ['label' => 'Criação do banco SQLite', 'value' => $dbOk ? $dbMsg : ($pdoSqliteOk ? $dbMsg : 'pdo_sqlite ausente'), 'ok' => $dbOk, 'fix' => 'Verifique permissões da pasta storage/ e se pdo_sqlite está ativo'];

// 12. Testa bootstrap real
$bootOk = false;
$bootError = '';
if ($appExists && $bootExists) {
    define('APP_PATH', $APP);
    define('PUBLIC_PATH', __DIR__);
    try {
        ob_start();
        require_once $APP . '/config/bootstrap.php';
        ob_end_clean();
        $bootOk = true;
    } catch (Throwable $e) {
        ob_end_clean();
        $bootError = $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine();
    }
}
$checks[] = ['label' => 'Bootstrap da aplicação', 'value' => $bootOk ? 'Carregou sem erros' : ($bootError ?: 'Não testado (app/ ausente)'), 'ok' => $bootOk, 'fix' => $bootError ?: 'Verifique se todas as pastas de app/ foram enviadas'];

// 13. .env legível
$envContent = '';
if ($envExists) {
    require_once $APP . '/config/env.php';
    loadEnv($envPath);
    $envContent = 'HOTEL_NAME=' . env('HOTEL_NAME', '(não definido)') . ' | SITE_URL=' . env('SITE_URL', '(não definido)') . ' | PIX_API_URL=' . (env('PIX_API_URL') ? '✓ definida' : '(não definido)');
}
$checks[] = ['label' => 'Leitura do .env', 'value' => $envExists ? $envContent : 'Arquivo ausente', 'ok' => $envExists && $envContent !== '', 'fix' => 'Verifique se o .env está correto e na pasta app/'];

// 14. mod_rewrite (URL rewrite)
$rewriteOk = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true; // assume ok se não conseguir checar
$checks[] = ['label' => 'mod_rewrite (URLs amigáveis)', 'value' => $rewriteOk ? 'Ativo' : 'Não detectado', 'ok' => $rewriteOk, 'fix' => 'Na Hostinger já vem ativo. Se não funcionar, contate o suporte.'];

// ── Contagem ──
$total = count($checks);
$passed = count(array_filter($checks, fn($c) => $c['ok']));
$failed = $total - $passed;

?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Diagnóstico — Windsor Plaza</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; color: #1a1a1a; padding: 24px 16px; }
  .wrap { max-width: 760px; margin: 0 auto; }
  h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
  .subtitle { font-size: 14px; color: #666; margin-bottom: 24px; }
  .summary { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
  .badge { padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; }
  .badge-ok { background: #dcf5e8; color: #1a7a4c; }
  .badge-fail { background: #fef2f2; color: #c0392b; }
  .badge-total { background: #f0f0f0; color: #444; }
  .card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); margin-bottom: 8px; }
  .card-row { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-bottom: 1px solid #f0f0f0; }
  .card-row:last-child { border-bottom: none; }
  .icon { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-top: 1px; }
  .icon-ok { background: #dcf5e8; color: #1a7a4c; }
  .icon-fail { background: #fef2f2; color: #c0392b; }
  .info { flex: 1; min-width: 0; }
  .label { font-size: 13px; font-weight: 600; color: #444; margin-bottom: 2px; }
  .value { font-size: 13px; color: #222; word-break: break-all; }
  .value-fail { color: #c0392b; font-weight: 500; }
  .fix { margin-top: 6px; font-size: 12px; background: #fff9eb; color: #7a5311; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #e6a817; }
  .alert { padding: 16px; border-radius: 10px; font-size: 14px; margin-bottom: 24px; font-weight: 500; }
  .alert-ok { background: #dcf5e8; color: #1a7a4c; }
  .alert-fail { background: #fef2f2; color: #c0392b; }
  .delete-notice { margin-top: 24px; padding: 14px 16px; background: #fff3cd; border-radius: 10px; font-size: 13px; color: #7a5311; border-left: 4px solid #e6a817; }
  h2 { font-size: 15px; font-weight: 600; margin-bottom: 12px; color: #333; }
  .info-box { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.08); margin-bottom: 24px; font-size: 13px; line-height: 1.7; color: #444; }
  .info-box code { background: #f0f0f0; padding: 1px 5px; border-radius: 4px; font-size: 12px; }
</style>
</head>
<body>
<div class="wrap">
  <h1>🔍 Diagnóstico do Servidor</h1>
  <p class="subtitle">Windsor Plaza — Payment Recovery System</p>

  <div class="summary">
    <span class="badge badge-total">Total: <?= $total ?> verificações</span>
    <span class="badge badge-ok">✓ OK: <?= $passed ?></span>
    <?php if ($failed > 0): ?>
    <span class="badge badge-fail">✗ Falhas: <?= $failed ?></span>
    <?php endif; ?>
  </div>

  <?php if ($failed === 0): ?>
    <div class="alert alert-ok">✅ Tudo certo! O sistema está pronto para funcionar. Acesse /admin e faça login.</div>
  <?php else: ?>
    <div class="alert alert-fail">⚠️ <?= $failed ?> problema<?= $failed > 1 ? 's' : '' ?> encontrado<?= $failed > 1 ? 's' : '' ?>. Siga as instruções abaixo para corrigir.</div>
  <?php endif; ?>

  <h2>Resultados</h2>
  <div class="card">
    <?php foreach ($checks as $c): ?>
    <div class="card-row">
      <div class="icon <?= $c['ok'] ? 'icon-ok' : 'icon-fail' ?>"><?= $c['ok'] ? '✓' : '✗' ?></div>
      <div class="info">
        <div class="label"><?= htmlspecialchars($c['label']) ?></div>
        <div class="value <?= $c['ok'] ? '' : 'value-fail' ?>"><?= htmlspecialchars($c['value']) ?></div>
        <?php if (!$c['ok'] && $c['fix']): ?>
          <div class="fix">💡 <?= htmlspecialchars($c['fix']) ?></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <h2 style="margin-top:24px">Informações do Servidor</h2>
  <div class="info-box">
    <b>PHP:</b> <?= PHP_VERSION ?> | <b>OS:</b> <?= PHP_OS ?> | <b>SAPI:</b> <?= PHP_SAPI ?><br>
    <b>Document root:</b> <code><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></code><br>
    <b>Script:</b> <code><?= __FILE__ ?></code><br>
    <b>APP_PATH calculado:</b> <code><?= $APP ?></code><br>
    <b>Diretório existe:</b> <?= is_dir($APP) ? '✓ Sim' : '✗ Não' ?><br>
    <b>Extensões PDO:</b> <?= implode(', ', array_filter(get_loaded_extensions(), fn($e) => stripos($e, 'pdo') !== false)) ?: 'Nenhuma encontrada' ?>
  </div>

  <div class="delete-notice">
    ⚠️ <strong>Importante:</strong> Após resolver todos os problemas e confirmar que o site funciona,
    <strong>apague este arquivo</strong> (<code>check.php</code>) do servidor por segurança.
  </div>
</div>
</body>
</html>
