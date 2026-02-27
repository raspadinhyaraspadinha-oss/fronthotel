<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — <?= e(env('HOTEL_NAME', 'Windsor Plaza')) ?></title>
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <style>
        .admin-layout { min-height: 100vh; background: var(--bg); }

        .admin-header {
            border-bottom: 1px solid var(--border-light);
            padding: var(--sp-4) var(--sp-6);
            position: sticky; top: 0; z-index: 100;
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            background: rgba(255,255,255,0.85);
        }
        .admin-header-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .admin-logo { height: 32px; }
        .admin-logo-text { font-size: 18px; font-weight: 700; color: var(--gold); letter-spacing: 0.02em; }
        .admin-main { max-width: 1200px; margin: 0 auto; padding: var(--sp-8) var(--sp-6); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--sp-4); margin-bottom: var(--sp-8); }
        .stat-card { background: var(--surface); border-radius: var(--radius-lg); padding: var(--sp-6); border: 1px solid var(--border-light); box-shadow: var(--shadow-xs); }
        .stat-label { font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: var(--sp-2); }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text); line-height: 1.2; }
        .stat-value-gold { color: var(--gold); }
        .stat-value-success { color: var(--success); }
        .stat-value-warning { color: var(--warning); }

        .actions-bar { background: var(--surface); border-radius: var(--radius-lg); padding: var(--sp-6); border: 1px solid var(--border-light); box-shadow: var(--shadow-xs); margin-bottom: var(--sp-6); }
        .actions-row { display: flex; align-items: center; gap: var(--sp-4); flex-wrap: wrap; }
        .upload-area { flex: 1; min-width: 280px; }
        .upload-label {
            display: flex; align-items: center; justify-content: center; gap: var(--sp-3);
            padding: var(--sp-5) var(--sp-6); border: 2px dashed var(--border); border-radius: var(--radius-md);
            cursor: pointer; transition: all var(--duration) var(--ease); color: var(--muted); font-size: 14px; font-weight: 500;
        }
        .upload-label:hover, .upload-label.dragover { border-color: var(--gold); color: var(--gold); background: var(--accent-subtle); }
        .upload-label svg { width: 20px; height: 20px; flex-shrink: 0; }
        .upload-file-input { display: none; }

        .filter-bar { display: flex; gap: var(--sp-3); margin-bottom: var(--sp-6); flex-wrap: wrap; }
        .filter-bar .input { max-width: 320px; }
        .filter-pills { display: flex; gap: var(--sp-2); flex-wrap: wrap; }
        .filter-pill {
            padding: var(--sp-2) var(--sp-4); border-radius: var(--radius-pill); font-size: 13px; font-weight: 500;
            border: 1px solid var(--border); background: var(--surface); color: var(--text-secondary);
            cursor: pointer; transition: all var(--duration) var(--ease); text-decoration: none; min-height: 36px;
            display: inline-flex; align-items: center;
        }
        .filter-pill:hover { border-color: var(--gold-light); color: var(--gold-dark); }
        .filter-pill.active { background: var(--gold); color: white; border-color: var(--gold); }

        .table-container { background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); box-shadow: var(--shadow-xs); overflow: hidden; }
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead { background: var(--surface2); }
        th { padding: var(--sp-3) var(--sp-4); text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); white-space: nowrap; border-bottom: 1px solid var(--border-light); }
        td { padding: var(--sp-3) var(--sp-4); border-bottom: 1px solid var(--border-light); white-space: nowrap; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--surface2); }
        .guest-name { font-weight: 600; color: var(--text); }

        .copy-link {
            display: inline-flex; align-items: center; gap: var(--sp-1);
            padding: 4px 10px; border-radius: var(--radius-pill); font-size: 12px; font-weight: 500;
            background: var(--gold-bg); color: var(--gold-dark); border: none; cursor: pointer;
            transition: all var(--duration) var(--ease); white-space: nowrap;
        }
        .copy-link:hover { background: var(--gold-bg2); }
        .copy-link svg { width: 14px; height: 14px; }

        .empty-state { text-align: center; padding: var(--sp-16) var(--sp-8); color: var(--muted); }
        .empty-state svg { width: 48px; height: 48px; margin-bottom: var(--sp-4); opacity: 0.3; }

        .toggle-wrapper { display: flex; align-items: center; gap: var(--sp-3); }
        .toggle-label { font-size: 14px; color: var(--text-secondary); }
        .toggle {
            appearance: none; width: 44px; height: 26px; background: var(--border);
            border-radius: 13px; position: relative; cursor: pointer;
            transition: background var(--duration) var(--ease); border: none; flex-shrink: 0;
        }
        .toggle::after {
            content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px;
            background: white; border-radius: 50%; transition: transform var(--duration) var(--ease);
            box-shadow: var(--shadow-sm);
        }
        .toggle:checked { background: var(--gold); }
        .toggle:checked::after { transform: translateX(18px); }

        .alert-banner { padding: var(--sp-3) var(--sp-4); border-radius: var(--radius-md); font-size: 14px; margin-bottom: var(--sp-4); display: flex; align-items: center; gap: var(--sp-3); }
        .alert-success { background: var(--success-bg); color: #1a7a4c; }
        .alert-error { background: var(--danger-bg); color: var(--danger); }
        .alert-info { background: var(--info-bg); color: var(--info); }

        @media (max-width: 768px) {
            .admin-main { padding: var(--sp-4); }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .actions-row { flex-direction: column; align-items: stretch; }
            .upload-area { min-width: 100%; }
            .filter-bar { flex-direction: column; }
            .filter-bar .input { max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <header class="admin-header">
        <div class="admin-header-inner">
            <div class="flex items-center gap-4">
                <img src="/assets/img/logo.png" alt="" class="admin-logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                <span class="admin-logo-text" style="display:none"><?= e(env('HOTEL_NAME')) ?></span>
                <span style="font-size:13px;color:var(--muted);font-weight:500;padding:3px 10px;background:var(--surface2);border-radius:var(--radius-pill)">Admin</span>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="/admin/toggle-card" style="display:inline">
                    <?= csrfField() ?>
                    <div class="toggle-wrapper">
                        <span class="toggle-label">Botão Cartão</span>
                        <input type="checkbox" class="toggle" <?= $cardButtonActive ? 'checked' : '' ?>
                               onchange="this.form.submit()" aria-label="Ativar botão de cartão de crédito">
                    </div>
                </form>
                <a href="/admin/logout" class="btn btn-ghost btn-sm">Sair</a>
            </div>
        </div>
    </header>

    <main class="admin-main">
        <?php
        $msg = $_GET['msg'] ?? '';
        $msgMap = [
            'imported'    => ['success', (int)($_GET['count'] ?? 0) . ' reservas importadas' . ((int)($_GET['errs'] ?? 0) > 0 ? ' (' . (int)$_GET['errs'] . ' erros)' : '')],
            'deleted'     => ['info', 'Todas as reservas foram removidas.'],
            'toggled'     => ['success', 'Configuração atualizada.'],
            'upload_error'=> ['error', 'Erro no upload do arquivo.'],
            'csv_empty'   => ['error', 'Arquivo CSV vazio.'],
            'csv_columns' => ['error', 'Colunas obrigatórias não encontradas (nome, checkin, checkout).'],
        ];
        if (isset($msgMap[$msg])):
            [$type, $text] = $msgMap[$msg];
        ?>
            <div class="alert-banner alert-<?= $type ?>"><?= e($text) ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total de reservas</div>
                <div class="stat-value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pendentes</div>
                <div class="stat-value stat-value-warning"><?= $stats['pending'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pagas</div>
                <div class="stat-value stat-value-success"><?= $stats['paid'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Valor recuperado</div>
                <div class="stat-value stat-value-gold">R$ <?= formatBRL($stats['recovered_value']) ?></div>
            </div>
        </div>

        <div class="actions-bar">
            <div class="actions-row">
                <form method="POST" action="/admin/upload" enctype="multipart/form-data" class="upload-area" id="uploadForm">
                    <?= csrfField() ?>
                    <label class="upload-label" for="csvInput" id="uploadLabel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span id="uploadText">Importar CSV de reservas</span>
                    </label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="upload-file-input" id="csvInput">
                </form>

                <a href="/admin/export" class="btn btn-secondary btn-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Exportar
                </a>

                <form method="POST" action="/admin/delete-all" onsubmit="return confirm('Tem certeza? Isso removerá TODAS as reservas.')">
                    <?= csrfField() ?>
                    <button type="submit" class="btn btn-ghost btn-sm text-danger">Limpar tudo</button>
                </form>
            </div>
        </div>

        <div class="filter-bar">
            <form method="GET" action="/admin" class="flex gap-3 items-center w-full" style="flex-wrap:wrap">
                <input type="text" name="search" class="input" placeholder="Buscar por nome, código..."
                       value="<?= e($search) ?>" aria-label="Buscar reservas">
                <div class="filter-pills">
                    <a href="/admin" class="filter-pill <?= $statusFilter === '' ? 'active' : '' ?>">Todas</a>
                    <a href="/admin?status=pending<?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="filter-pill <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pendentes</a>
                    <a href="/admin?status=paid<?= $search ? '&search=' . urlencode($search) : '' ?>"
                       class="filter-pill <?= $statusFilter === 'paid' ? 'active' : '' ?>">Pagas</a>
                </div>
            </form>
        </div>

        <div class="table-container">
            <?php if (empty($reservations)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    <p class="text-heading" style="margin-bottom:var(--sp-2)">Nenhuma reserva</p>
                    <p class="text-caption">Importe um CSV para começar</p>
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead><tr>
                            <th>Código</th><th>Hóspede</th><th>Check-in</th><th>Check-out</th>
                            <th>Valor</th><th>Cartão</th><th>Status</th><th>Link</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><span style="font-family:monospace;font-size:13px;font-weight:600;color:var(--gold-dark);background:var(--gold-bg);padding:2px 8px;border-radius:var(--radius-pill)"><?= e($r['code']) ?></span></td>
                                <td class="guest-name"><?= e($r['guest_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['checkin'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['checkout'])) ?></td>
                                <td>R$ <?= formatBRL((float)$r['rate_total']) ?></td>
                                <td><span style="color:var(--muted)"><?= $r['card_last4'] ? '•••• ' . e($r['card_last4']) : '—' ?></span></td>
                                <td>
                                    <?php
                                    $sm = ['pending'=>['Pendente','badge-pending'],'paid'=>['Pago','badge-paid'],'expired'=>['Expirado','badge-expired']];
                                    $s = $sm[$r['status']] ?? $sm['pending'];
                                    ?>
                                    <span class="badge <?= $s[1] ?>"><?= $s[0] ?></span>
                                </td>
                                <td>
                                    <button class="copy-link" data-link="<?= e($siteUrl . '/r/' . $r['code']) ?>" aria-label="Copiar link da reserva">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                        <span>Copiar link</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
const csvInput = document.getElementById('csvInput');
const uploadForm = document.getElementById('uploadForm');
const uploadLabel = document.getElementById('uploadLabel');
const uploadText = document.getElementById('uploadText');

csvInput.addEventListener('change', () => {
    if (csvInput.files.length > 0) {
        uploadText.textContent = csvInput.files[0].name;
        uploadForm.submit();
    }
});

['dragover','dragenter'].forEach(ev => uploadLabel.addEventListener(ev, e => { e.preventDefault(); uploadLabel.classList.add('dragover'); }));
['dragleave','drop'].forEach(ev => uploadLabel.addEventListener(ev, () => uploadLabel.classList.remove('dragover')));
uploadLabel.addEventListener('drop', e => {
    e.preventDefault();
    csvInput.files = e.dataTransfer.files;
    if (csvInput.files.length) { uploadText.textContent = csvInput.files[0].name; uploadForm.submit(); }
});

document.querySelectorAll('.copy-link').forEach(btn => {
    btn.addEventListener('click', () => {
        const link = btn.dataset.link;
        navigator.clipboard.writeText(link).then(() => {
            const span = btn.querySelector('span');
            const orig = span.textContent;
            span.textContent = 'Copiado!';
            btn.style.background = 'var(--success-bg)';
            btn.style.color = 'var(--success)';
            showToast('Link copiado!');
            setTimeout(() => { span.textContent = orig; btn.style.background = ''; btn.style.color = ''; }, 2000);
        });
    });
});

function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.className = 'toast toast-' + type;
    t.textContent = msg;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

document.querySelector('.filter-bar input')?.addEventListener('keydown', e => { if (e.key === 'Enter') e.target.form.submit(); });
</script>
</body>
</html>
