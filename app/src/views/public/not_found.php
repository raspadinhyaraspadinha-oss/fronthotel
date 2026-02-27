<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva não encontrada — <?= e(env('HOTEL_NAME', 'Windsor Plaza')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(180deg, #faf6ef 0%, #f0ece5 100%);
            text-align: center;
            padding: var(--sp-8);
        }

        .nf-logo {
            height: 40px;
            margin-bottom: var(--sp-12);
        }

        .nf-code {
            font-size: 64px;
            font-weight: 800;
            color: var(--gold);
            line-height: 1;
            margin-bottom: var(--sp-4);
            letter-spacing: -0.03em;
        }

        .nf-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: var(--sp-3);
        }

        .nf-text {
            font-size: 15px;
            color: var(--text-secondary);
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: var(--sp-8);
        }

        .nf-contact {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        .nf-contact a {
            color: var(--gold);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <img src="/assets/img/logo.png" alt="<?= e(env('HOTEL_NAME')) ?>" class="nf-logo"
         onerror="this.style.display='none'">

    <div class="nf-code">404</div>
    <h1 class="nf-title">Reserva não encontrada</h1>
    <p class="nf-text">
        O link que você acessou não corresponde a nenhuma reserva ativa.
        Verifique se o endereço está correto ou entre em contato conosco.
    </p>

    <?php $phone = env('HOTEL_PHONE', ''); $email = env('HOTEL_EMAIL', ''); ?>
    <?php if ($phone || $email): ?>
        <div class="nf-contact">
            <?php if ($phone): ?>Telefone: <?= e($phone) ?><br><?php endif; ?>
            <?php if ($email): ?>Email: <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
