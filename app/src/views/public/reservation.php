<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme sua reserva — <?= e($hotelName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <style>
        body {
            background: linear-gradient(180deg, #faf6ef 0%, #f5f2ed 40%, #f0ece5 100%);
            min-height: 100vh;
        }

        /* ── Header ── */
        .page-header {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(201,169,110,0.12);
            padding: var(--sp-4) var(--sp-6);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-inner {
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-logo {
            height: 36px;
        }

        .header-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 0.03em;
        }

        /* ── Main Content ── */
        .page-content {
            max-width: 640px;
            margin: 0 auto;
            padding: var(--sp-8) var(--sp-6) var(--sp-16);
        }

        /* ── Status Banner ── */
        .status-banner {
            display: flex;
            align-items: center;
            gap: var(--sp-3);
            padding: var(--sp-4) var(--sp-5);
            border-radius: var(--radius-lg);
            margin-bottom: var(--sp-6);
            font-size: 14px;
            font-weight: 500;
        }

        .status-banner-pending {
            background: linear-gradient(135deg, #fff9eb 0%, #fef3d4 100%);
            color: #7a5311;
            border: 1px solid rgba(230,168,23,0.2);
        }

        .status-banner-paid {
            background: linear-gradient(135deg, #f0faf5 0%, #dcf5e8 100%);
            color: #1a7a4c;
            border: 1px solid rgba(45,159,111,0.2);
        }

        .status-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e6a817;
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        /* ── Reservation Card ── */
        .reservation-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(201,169,110,0.1);
            overflow: hidden;
            margin-bottom: var(--sp-6);
        }

        .reservation-header {
            padding: var(--sp-8) var(--sp-8) var(--sp-6);
            text-align: center;
        }

        .reservation-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.3;
            margin-bottom: var(--sp-2);
            letter-spacing: -0.01em;
        }

        .reservation-subtitle {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .reservation-details {
            padding: 0 var(--sp-8) var(--sp-6);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--sp-1);
            background: var(--surface2);
            border-radius: var(--radius-lg);
            padding: var(--sp-5);
        }

        .detail-item {
            padding: var(--sp-3) var(--sp-3);
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.4;
        }

        .detail-item-full {
            grid-column: 1 / -1;
        }

        .value-highlight {
            background: var(--gold-bg);
            padding: var(--sp-4) var(--sp-5);
            border-radius: var(--radius-md);
            margin-top: var(--sp-4);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .value-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .value-amount {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .card-indicator {
            display: inline-flex;
            align-items: center;
            gap: var(--sp-2);
            font-size: 13px;
            color: var(--muted);
            margin-top: var(--sp-3);
        }

        .card-dots {
            letter-spacing: 2px;
        }

        /* ── Payment Section ── */
        .payment-section {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(201,169,110,0.1);
            overflow: hidden;
            margin-bottom: var(--sp-6);
        }

        .payment-inner {
            padding: var(--sp-8);
        }

        .payment-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            margin-bottom: var(--sp-6);
            text-align: center;
        }

        /* Pix Offer */
        .pix-offer {
            background: linear-gradient(135deg, #f0faf5 0%, #e8f7ef 50%, #f0faf5 100%);
            border: 1px solid rgba(45,159,111,0.15);
            border-radius: var(--radius-lg);
            padding: var(--sp-6);
            margin-bottom: var(--sp-6);
            text-align: center;
        }

        .pix-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--sp-2);
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            background: rgba(45,159,111,0.12);
            color: var(--success);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: var(--sp-4);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pix-price-original {
            font-size: 16px;
            color: var(--muted);
            text-decoration: line-through;
            margin-bottom: var(--sp-1);
        }

        .pix-price-new {
            font-size: 32px;
            font-weight: 800;
            color: var(--success);
            line-height: 1.2;
            margin-bottom: var(--sp-1);
            letter-spacing: -0.02em;
        }

        .pix-savings {
            font-size: 14px;
            color: var(--success);
            font-weight: 500;
        }

        /* CTA Pix */
        .btn-pix {
            background: linear-gradient(135deg, #2d9f6f 0%, #25875e 100%);
            color: white;
            padding: var(--sp-5) var(--sp-8);
            font-size: 17px;
            font-weight: 700;
            border-radius: var(--radius-lg);
            width: 100%;
            min-height: 56px;
            border: none;
            cursor: pointer;
            transition: all var(--duration) var(--ease);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--sp-3);
            position: relative;
            overflow: hidden;
            font-family: var(--font);
            margin-bottom: var(--sp-4);
            box-shadow: 0 4px 16px rgba(45,159,111,0.25);
        }

        .btn-pix:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(45,159,111,0.35);
        }

        .btn-pix:active {
            transform: translateY(0);
        }

        .btn-pix:disabled {
            opacity: 0.6;
            cursor: wait;
        }

        .btn-pix svg {
            width: 22px;
            height: 22px;
        }

        /* CTA Card */
        .btn-card {
            background: transparent;
            color: var(--text-secondary);
            padding: var(--sp-3) var(--sp-6);
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius-md);
            width: 100%;
            min-height: 44px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all var(--duration) var(--ease);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--sp-2);
            font-family: var(--font);
        }

        .btn-card:hover {
            border-color: var(--gold-light);
            color: var(--text);
        }

        .btn-card:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .btn-card:disabled:hover {
            border-color: var(--border);
            color: var(--text-secondary);
        }

        /* ── Trust Signals ── */
        .trust-signals {
            display: flex;
            justify-content: center;
            gap: var(--sp-6);
            padding: var(--sp-5) var(--sp-4);
            flex-wrap: wrap;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: var(--sp-2);
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }

        .trust-item svg {
            width: 16px;
            height: 16px;
            color: var(--success);
        }

        /* ── Urgency Strip ── */
        .urgency-strip {
            background: linear-gradient(135deg, var(--gold-bg) 0%, var(--gold-bg2) 100%);
            border: 1px solid rgba(201,169,110,0.15);
            border-radius: var(--radius-lg);
            padding: var(--sp-4) var(--sp-5);
            margin-bottom: var(--sp-6);
            text-align: center;
        }

        .urgency-text {
            font-size: 14px;
            color: var(--gold-dark);
            font-weight: 500;
            line-height: 1.5;
        }

        .urgency-timer {
            font-weight: 700;
            color: var(--gold-dark);
        }

        /* ── Social Proof ── */
        .social-proof {
            text-align: center;
            padding: var(--sp-3);
            font-size: 13px;
            color: var(--muted);
            margin-bottom: var(--sp-6);
        }

        .social-proof strong {
            color: var(--text-secondary);
        }

        /* ── FAQ Section ── */
        .faq-section {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(201,169,110,0.1);
            padding: var(--sp-8);
            margin-bottom: var(--sp-6);
        }

        .faq-title {
            font-size: 17px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: var(--sp-6);
            text-align: center;
        }

        .faq-item {
            padding: var(--sp-4) 0;
            border-bottom: 1px solid var(--border-light);
        }

        .faq-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .faq-item:first-child {
            padding-top: 0;
        }

        .faq-q {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: var(--sp-2);
        }

        .faq-a {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ── Footer ── */
        .page-footer {
            text-align: center;
            padding: var(--sp-8) var(--sp-6);
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .footer-hotel {
            font-weight: 600;
            color: var(--gold-dark);
        }

        /* ── Pix Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: var(--sp-6);
            animation: fadeIn 0.3s var(--ease);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 440px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.35s var(--ease);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--sp-6) var(--sp-6) var(--sp-4);
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: var(--surface2);
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--duration) var(--ease);
        }

        .modal-close:hover {
            background: var(--surface3);
            color: var(--text);
        }

        .modal-body {
            padding: 0 var(--sp-6) var(--sp-6);
            text-align: center;
        }

        .qr-container {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--sp-6);
            margin-bottom: var(--sp-4);
            border: 1px solid var(--border-light);
        }

        .qr-image {
            width: 220px;
            height: 220px;
            margin: 0 auto;
            display: block;
        }

        .qr-placeholder {
            width: 220px;
            height: 220px;
            margin: 0 auto;
            background: var(--surface2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 14px;
        }

        .pix-code-box {
            background: var(--surface2);
            border-radius: var(--radius-md);
            padding: var(--sp-3) var(--sp-4);
            margin-bottom: var(--sp-4);
            display: flex;
            align-items: center;
            gap: var(--sp-3);
        }

        .pix-code-text {
            flex: 1;
            font-size: 12px;
            font-family: monospace;
            color: var(--text-secondary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }

        .btn-copy {
            flex-shrink: 0;
            padding: var(--sp-2) var(--sp-4);
            border-radius: var(--radius-sm);
            border: none;
            background: var(--gold);
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: var(--font);
            transition: all var(--duration) var(--ease);
            min-height: 36px;
        }

        .btn-copy:hover {
            background: var(--accent-hover);
        }

        .modal-amount {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: var(--sp-4);
        }

        .modal-amount strong {
            color: var(--success);
            font-size: 18px;
        }

        .modal-timer {
            font-size: 13px;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--sp-2);
        }

        .status-checking {
            display: none;
            align-items: center;
            justify-content: center;
            gap: var(--sp-3);
            padding: var(--sp-4);
            font-size: 14px;
            color: var(--text-secondary);
        }

        .status-checking.active {
            display: flex;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid var(--border);
            border-top-color: var(--gold);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── Card Form Modal ── */
        .card-form {
            display: flex;
            flex-direction: column;
            gap: var(--sp-4);
        }

        .form-group {
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: var(--sp-2);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--sp-3);
        }

        /* ── Success State ── */
        .success-state {
            display: none;
            text-align: center;
            padding: var(--sp-12) var(--sp-8);
        }

        .success-state.active {
            display: block;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto var(--sp-6);
            background: var(--success-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s var(--ease);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            to { transform: scale(1); opacity: 1; }
        }

        .success-icon svg {
            width: 32px;
            height: 32px;
            color: var(--success);
        }

        .success-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: var(--sp-3);
        }

        .success-text {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ── Loading State ── */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .page-content {
                padding: var(--sp-4) var(--sp-4) var(--sp-12);
            }

            .reservation-header {
                padding: var(--sp-6) var(--sp-5) var(--sp-4);
            }

            .reservation-title {
                font-size: 20px;
            }

            .reservation-details {
                padding: 0 var(--sp-5) var(--sp-5);
            }

            .payment-inner {
                padding: var(--sp-6);
            }

            .pix-price-new {
                font-size: 28px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .trust-signals {
                gap: var(--sp-4);
            }

            .faq-section {
                padding: var(--sp-6);
            }

            .modal {
                margin: var(--sp-4);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="page-header">
        <div class="header-inner">
            <img src="/assets/img/logo.png" alt="<?= e($hotelName) ?>" class="header-logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
            <span class="header-logo-text" style="display:none"><?= e($hotelName) ?></span>
        </div>
    </header>

    <main class="page-content" id="mainContent">
        <?php if ($reservation['status'] === 'paid'): ?>
            <!-- ═══ PAID STATE ═══ -->
            <div class="status-banner status-banner-paid">
                <svg class="status-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                Reserva confirmada e paga
            </div>

            <div class="reservation-card">
                <div class="success-state active">
                    <div class="success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h1 class="success-title">Pagamento confirmado!</h1>
                    <p class="success-text">
                        Sua reserva está garantida.<br>
                        Aguardamos você no check-in.
                    </p>
                </div>

                <div class="reservation-details" style="padding-top:0">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Hóspede</div>
                            <div class="detail-value"><?= e($reservation['guest_name']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Noites</div>
                            <div class="detail-value"><?= $nights ?> noite<?= $nights > 1 ? 's' : '' ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-in</div>
                            <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['checkin'])) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-out</div>
                            <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['checkout'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ═══ PENDING STATE ═══ -->
            <div class="status-banner status-banner-pending">
                <span class="pulse-dot"></span>
                Reserva pendente de confirmação
            </div>

            <!-- Social Proof -->
            <div class="social-proof">
                <strong><?= $socialProofCount ?></strong> reservas confirmadas hoje
            </div>

            <!-- Reservation Card -->
            <div class="reservation-card">
                <div class="reservation-header">
                    <h1 class="reservation-title">Sua reserva está quase garantida</h1>
                    <p class="reservation-subtitle">
                        Identificamos um problema com o pagamento. Finalize abaixo para confirmar sua estadia.
                    </p>
                </div>

                <div class="reservation-details">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Hóspede</div>
                            <div class="detail-value"><?= e($reservation['guest_name']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><?= $nights ?> noite<?= $nights > 1 ? 's' : '' ?></div>
                            <div class="detail-value"><?= e($reservation['confirmation_number'] ?: $reservation['code']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-in</div>
                            <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['checkin'])) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-out</div>
                            <div class="detail-value"><?= date('d/m/Y', strtotime($reservation['checkout'])) ?></div>
                        </div>
                    </div>

                    <div class="value-highlight">
                        <span class="value-label">Valor total da reserva</span>
                        <span class="value-amount">R$ <?= number_format($originalValue, 2, ',', '.') ?></span>
                    </div>

                    <?php if ($reservation['card_last4']): ?>
                        <div class="card-indicator">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <span>Cartão tentado: <span class="card-dots">••••</span> <?= e($reservation['card_last4']) ?> — pagamento não aprovado</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Urgency -->
            <div class="urgency-strip">
                <p class="urgency-text">
                    Para manter esta tarifa e garantir sua reserva, finalize o pagamento em até
                    <span class="urgency-timer" id="urgencyTimer"><?= $expiryHours ?>h</span>
                </p>
            </div>

            <!-- Payment Section -->
            <div class="payment-section" id="paymentSection">
                <div class="payment-inner">
                    <div class="payment-title">Escolha como pagar</div>

                    <!-- Pix Offer -->
                    <div class="pix-offer">
                        <div class="pix-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <?= $pixDiscount ?>% de desconto no Pix
                        </div>
                        <div class="pix-price-original">R$ <?= number_format($originalValue, 2, ',', '.') ?></div>
                        <div class="pix-price-new">R$ <?= number_format($discountedValue, 2, ',', '.') ?></div>
                        <div class="pix-savings">Economia de R$ <?= number_format($savings, 2, ',', '.') ?></div>
                    </div>

                    <!-- CTA Pix -->
                    <button class="btn-pix" id="btnPix" onclick="initPixPayment()" aria-label="Pagar com Pix com desconto">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Pagar agora com Pix
                    </button>

                    <!-- Trust -->
                    <div class="trust-signals">
                        <span class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Pagamento seguro
                        </span>
                        <span class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Confirmação imediata
                        </span>
                        <span class="trust-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Sem burocracia
                        </span>
                    </div>

                    <hr class="divider">

                    <!-- CTA Card -->
                    <button class="btn-card" id="btnCard"
                            <?= !$cardButtonActive ? 'disabled' : '' ?>
                            <?= $cardButtonActive ? 'onclick="openCardModal()"' : '' ?>
                            aria-label="Alterar cartão de crédito">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Alterar cartão de crédito
                    </button>
                </div>
            </div>

            <!-- FAQ -->
            <div class="faq-section">
                <h2 class="faq-title">Por que estamos pedindo isso?</h2>

                <div class="faq-item">
                    <div class="faq-q">O que aconteceu com meu pagamento?</div>
                    <div class="faq-a">O cartão informado na reserva não foi aprovado pela operadora. Isso pode ocorrer por limite, bloqueio temporário ou dados divergentes. Sua reserva continua salva, mas precisa de uma nova forma de pagamento para ser confirmada.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Por que o Pix tem desconto?</div>
                    <div class="faq-a">O Pix tem custo operacional menor para o hotel, e repassamos essa economia para você como incentivo. O pagamento é instantâneo e sua reserva é confirmada na hora.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Meus dados estão seguros?</div>
                    <div class="faq-a">Sim. Utilizamos conexão criptografada e não armazenamos dados sensíveis de cartão. O pagamento via Pix é processado diretamente pelo sistema bancário brasileiro.</div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="page-footer">
        <span class="footer-hotel"><?= e($hotelName) ?></span><br>
        <?php if ($hotelPhone): ?>
            <?= e($hotelPhone) ?><br>
        <?php endif; ?>
        <?php if ($hotelEmail): ?>
            <?= e($hotelEmail) ?>
        <?php endif; ?>
    </footer>

    <!-- Pix Modal -->
    <div class="modal-overlay" id="pixModal" role="dialog" aria-modal="true" aria-label="Pagamento via Pix">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Pagamento Pix</span>
                <button class="modal-close" onclick="closePixModal()" aria-label="Fechar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-amount">
                    Valor a pagar: <strong>R$ <?= number_format($discountedValue, 2, ',', '.') ?></strong>
                </div>

                <div class="qr-container" id="qrContainer">
                    <div class="qr-placeholder" id="qrPlaceholder">
                        <div class="spinner"></div>
                    </div>
                    <img id="qrImage" class="qr-image" style="display:none" alt="QR Code Pix">
                </div>

                <div class="pix-code-box" id="pixCodeBox" style="display:none">
                    <span class="pix-code-text" id="pixCodeText"></span>
                    <button class="btn-copy" onclick="copyPixCode()" aria-label="Copiar código Pix">Copiar</button>
                </div>

                <div class="status-checking" id="statusChecking">
                    <div class="spinner"></div>
                    <span>Aguardando pagamento...</span>
                </div>

                <div class="modal-timer" id="modalTimer"></div>
            </div>
        </div>
    </div>

    <!-- Card Modal -->
    <?php if ($cardButtonActive): ?>
    <div class="modal-overlay" id="cardModal" role="dialog" aria-modal="true" aria-label="Alterar cartão de crédito">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Alterar cartão</span>
                <button class="modal-close" onclick="closeCardModal()" aria-label="Fechar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-form" onsubmit="handleCardSubmit(event)">
                    <div class="form-group">
                        <label class="form-label" for="cardNumber">Número do cartão</label>
                        <input type="text" id="cardNumber" class="input" placeholder="0000 0000 0000 0000"
                               maxlength="19" inputmode="numeric" autocomplete="cc-number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cardName">Nome no cartão</label>
                        <input type="text" id="cardName" class="input" placeholder="Como está no cartão"
                               autocomplete="cc-name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cardExpiry">Validade</label>
                            <input type="text" id="cardExpiry" class="input" placeholder="MM/AA"
                                   maxlength="5" inputmode="numeric" autocomplete="cc-exp" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cardCvc">CVV</label>
                            <input type="text" id="cardCvc" class="input" placeholder="123"
                                   maxlength="4" inputmode="numeric" autocomplete="cc-csc" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-full" id="btnCardSubmit">
                        Processar pagamento
                    </button>
                    <p style="font-size:12px;color:var(--muted);margin-top:var(--sp-2)">
                        Seus dados são criptografados e processados com segurança.
                    </p>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Success Overlay -->
    <div class="modal-overlay" id="successModal" role="dialog" aria-modal="true" aria-label="Pagamento confirmado">
        <div class="modal" style="text-align:center;padding:var(--sp-12) var(--sp-8)">
            <div class="success-icon" style="margin:0 auto var(--sp-6)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h2 class="success-title">Pagamento confirmado!</h2>
            <p class="success-text" style="margin-bottom:var(--sp-6)">
                Sua reserva no <?= e($hotelName) ?> está garantida.<br>
                Aguardamos você no check-in!
            </p>
            <button class="btn btn-primary btn-lg" onclick="location.reload()">Ver reserva</button>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        const RESERVATION_CODE = '<?= e($reservation['code']) ?>';
        let pixPollingInterval = null;
        let pixTransactionId = null;

        // ── Pix Payment ──
        async function initPixPayment() {
            const btn = document.getElementById('btnPix');
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btn.innerHTML = '<span style="color:transparent">Processando...</span>';

            try {
                const resp = await fetch('/api/pix/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ code: RESERVATION_CODE })
                });

                const data = await resp.json();

                if (!resp.ok || !data.success) {
                    throw new Error(data.error || 'Erro ao gerar Pix');
                }

                pixTransactionId = data.transaction_id;

                // Show modal
                openPixModal();

                // Display QR Code
                const qrPlaceholder = document.getElementById('qrPlaceholder');
                const qrImage = document.getElementById('qrImage');

                if (data.qr_code_base64) {
                    qrImage.src = data.qr_code_base64;
                    qrImage.style.display = 'block';
                    qrPlaceholder.style.display = 'none';
                }

                // Display copy code
                if (data.qr_code) {
                    document.getElementById('pixCodeText').textContent = data.qr_code;
                    document.getElementById('pixCodeBox').style.display = 'flex';
                }

                // Show status checking
                document.getElementById('statusChecking').classList.add('active');

                // Start polling
                startPixPolling();

            } catch (err) {
                showToast(err.message || 'Erro ao processar pagamento', 'error');
            } finally {
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.innerHTML = `
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Pagar agora com Pix
                `;
            }
        }

        function startPixPolling() {
            if (pixPollingInterval) clearInterval(pixPollingInterval);

            pixPollingInterval = setInterval(async () => {
                try {
                    const resp = await fetch(`/api/pix/status?code=${RESERVATION_CODE}`);
                    const data = await resp.json();

                    if (data.status === 'PAID') {
                        clearInterval(pixPollingInterval);
                        closePixModal();
                        showSuccessModal();
                    } else if (data.status === 'EXPIRED' || data.status === 'REJECTED') {
                        clearInterval(pixPollingInterval);
                        document.getElementById('statusChecking').innerHTML =
                            '<span style="color:var(--danger)">Pagamento ' + (data.status === 'EXPIRED' ? 'expirado' : 'rejeitado') + '. Tente novamente.</span>';
                    }
                } catch (e) {
                    // Silent fail, keep polling
                }
            }, 5000);
        }

        function copyPixCode() {
            const code = document.getElementById('pixCodeText').textContent;
            navigator.clipboard.writeText(code).then(() => {
                showToast('Código Pix copiado!', 'success');
                const btn = document.querySelector('.btn-copy');
                btn.textContent = 'Copiado!';
                setTimeout(() => btn.textContent = 'Copiar', 2000);
            }).catch(() => {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = code;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToast('Código Pix copiado!', 'success');
            });
        }

        // ── Modals ──
        function openPixModal() {
            document.getElementById('pixModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePixModal() {
            document.getElementById('pixModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function openCardModal() {
            document.getElementById('cardModal')?.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCardModal() {
            document.getElementById('cardModal')?.classList.remove('active');
            document.body.style.overflow = '';
        }

        function showSuccessModal() {
            document.getElementById('successModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close modals on backdrop click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => {
                    m.classList.remove('active');
                });
                document.body.style.overflow = '';
            }
        });

        // ── Card Form ──
        function handleCardSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('btnCardSubmit');
            btn.disabled = true;
            btn.textContent = 'Processando...';

            // Simulate - ready for gateway integration
            setTimeout(() => {
                showToast('Funcionalidade em integração. Use o Pix para confirmação imediata.', 'error');
                btn.disabled = false;
                btn.textContent = 'Processar pagamento';
            }, 2000);
        }

        // Card number formatting
        document.getElementById('cardNumber')?.addEventListener('input', function(e) {
            let v = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
        });

        document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
            let v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
            this.value = v;
        });

        // ── Toast ──
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // ── Urgency Timer ──
        (function() {
            const timerEl = document.getElementById('urgencyTimer');
            if (!timerEl) return;

            let hours = <?= $expiryHours ?>;
            let totalSeconds = hours * 3600;

            function updateTimer() {
                if (totalSeconds <= 0) {
                    timerEl.textContent = 'Expirado';
                    return;
                }

                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60);
                timerEl.textContent = h + 'h ' + String(m).padStart(2, '0') + 'min';
                totalSeconds--;
            }

            updateTimer();
            setInterval(updateTimer, 60000);
        })();
    </script>
</body>
</html>
