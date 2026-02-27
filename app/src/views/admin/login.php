<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= e(env('HOTEL_NAME', 'Windsor Plaza')) ?></title>
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--bg);
        }
        .login-container { width: 100%; max-width: 400px; padding: var(--sp-6); }
        .login-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: var(--sp-12) var(--sp-8);
            text-align: center;
        }
        .login-logo { width: 180px; margin: 0 auto var(--sp-8); display: block; }
        .login-subtitle { font-size: 15px; color: var(--muted); margin-bottom: var(--sp-8); }
        .login-error {
            background: var(--danger-bg); color: var(--danger);
            padding: var(--sp-3) var(--sp-4); border-radius: var(--radius-sm);
            font-size: 14px; margin-bottom: var(--sp-4);
        }
        .login-form { display: flex; flex-direction: column; gap: var(--sp-4); }
        .login-input { text-align: center; font-size: 16px; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <img src="/assets/img/logo.png" alt="<?= e(env('HOTEL_NAME')) ?>" class="login-logo"
                 onerror="this.style.display='none';document.getElementById('logo-text').style.display='block'">
            <div id="logo-text" style="display:none;margin-bottom:var(--sp-8)">
                <span style="font-size:24px;font-weight:700;color:var(--gold);letter-spacing:0.02em"><?= e(env('HOTEL_NAME', 'Windsor Plaza')) ?></span>
            </div>

            <p class="login-subtitle">Painel administrativo</p>

            <?php if (!empty($error)): ?>
                <div class="login-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/admin/login" class="login-form">
                <input type="password" name="password" class="input login-input"
                       placeholder="Senha de acesso" required autofocus
                       autocomplete="current-password" aria-label="Senha de acesso ao painel">
                <button type="submit" class="btn btn-primary btn-lg w-full">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
