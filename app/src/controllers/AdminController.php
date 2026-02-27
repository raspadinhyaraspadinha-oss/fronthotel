<?php

class AdminController
{
    private static function requireAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['admin_auth'])) {
            redirect('/admin/login');
        }
    }

    public static function loginForm(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!empty($_SESSION['admin_auth'])) {
            redirect('/admin');
        }
        $error = '';
        require APP_PATH . '/src/views/admin/login.php';
    }

    public static function loginSubmit(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $password = $_POST['password'] ?? '';
        $storedHash = env('ADMIN_PASSWORD_HASH', '');

        // Support both hashed and plain text passwords
        if ($storedHash && password_verify($password, $storedHash)) {
            $_SESSION['admin_auth'] = true;
            session_regenerate_id(true);
            redirect('/admin');
        } elseif (!$storedHash && $password === env('ADMIN_PASSWORD', 'admin123')) {
            $_SESSION['admin_auth'] = true;
            session_regenerate_id(true);
            redirect('/admin');
        }

        $error = 'Senha incorreta';
        require APP_PATH . '/src/views/admin/login.php';
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        session_destroy();
        redirect('/admin/login');
    }

    public static function dashboard(): void
    {
        self::requireAuth();

        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';
        $allowedStatuses = ['pending', 'paid', 'expired', ''];
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = '';
        }

        $reservations = Reservation::findAll($statusFilter, $search);
        $stats = Reservation::getStats();
        $cardButtonActive = Setting::get('card_button_active', '0') === '1';
        $siteUrl = rtrim(env('SITE_URL', 'http://localhost:8080'), '/');

        require APP_PATH . '/src/views/admin/dashboard.php';
    }

    public static function upload(): void
    {
        self::requireAuth();
        requireCsrf();

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            redirect('/admin?msg=upload_error');
        }

        $content = file_get_contents($_FILES['csv_file']['tmp_name']);

        // Fix encoding
        $enc = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($enc && $enc !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $enc);
        }
        // Remove BOM
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $lines = array_values(array_filter(explode("\n", $content), fn($l) => trim($l) !== ''));

        if (count($lines) < 2) {
            redirect('/admin?msg=csv_empty');
        }

        // Detect separator
        $header = $lines[0];
        $separator = (substr_count($header, ';') >= substr_count($header, ',')) ? ';' : ',';

        $headers = array_map(fn($h) => strtolower(trim($h)), str_getcsv($header, $separator));

        $colMap = [
            'confirmation_number' => self::findCol($headers, ['confirmation number', 'confirmation_number', 'confirmacao', 'codigo']),
            'guest_name'          => self::findCol($headers, ['name', 'nome', 'guest_name', 'hospede']),
            'checkin'             => self::findCol($headers, ['arrival', 'checkin', 'check-in', 'check_in', 'entrada']),
            'checkout'            => self::findCol($headers, ['departure', 'checkout', 'check-out', 'check_out', 'saida']),
            'rate_total'          => self::findCol($headers, ['rate total (estimated)', 'rate total', 'rate_total', 'valor_total', 'valor', 'total']),
            'card_last4'          => self::findCol($headers, ['card last 4', 'card_last_4', 'card last4', 'cartao_ult4', 'last4', 'card_last4']),
            'email'               => self::findCol($headers, ['email', 'e-mail']),
            'phone'               => self::findCol($headers, ['phone', 'telefone', 'tel']),
        ];

        if ($colMap['guest_name'] === -1 || $colMap['checkin'] === -1 || $colMap['checkout'] === -1) {
            redirect('/admin?msg=csv_columns');
        }

        $imported = 0;
        $errors = 0;

        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i], $separator);
            if (count($row) < 3) continue;

            try {
                $guestName = self::getCol($row, $colMap['guest_name']);
                // Invert "Last, First" → "First Last"
                if (strpos($guestName, ',') !== false) {
                    $parts = array_map('trim', explode(',', $guestName, 2));
                    $guestName = trim(($parts[1] ?? '') . ' ' . $parts[0]);
                }

                $checkin = self::parseDate(self::getCol($row, $colMap['checkin']));
                $checkout = self::parseDate(self::getCol($row, $colMap['checkout']));
                $rateTotal = self::parseBRL(self::getCol($row, $colMap['rate_total']));

                if ($guestName === '' || $checkin === '' || $checkout === '') {
                    $errors++;
                    continue;
                }

                Reservation::create([
                    'confirmation_number' => self::getCol($row, $colMap['confirmation_number']),
                    'guest_name'          => $guestName,
                    'checkin'             => $checkin,
                    'checkout'            => $checkout,
                    'rate_total'          => $rateTotal,
                    'card_last4'          => self::getCol($row, $colMap['card_last4']),
                    'email'               => self::getCol($row, $colMap['email']),
                    'phone'               => self::getCol($row, $colMap['phone']),
                ]);
                $imported++;
            } catch (\Exception $ex) {
                $errors++;
            }
        }

        redirect("/admin?msg=imported&count=$imported&errs=$errors");
    }

    public static function toggleCard(): void
    {
        self::requireAuth();
        requireCsrf();
        Setting::toggle('card_button_active');
        redirect('/admin?msg=toggled');
    }

    public static function deleteAll(): void
    {
        self::requireAuth();
        requireCsrf();
        Reservation::deleteAll();
        redirect('/admin?msg=deleted');
    }

    public static function export(): void
    {
        self::requireAuth();

        $reservations = Reservation::findAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reservas_' . date('Y-m-d_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
        fputcsv($out, ['Código', 'Confirmação', 'Hóspede', 'Check-in', 'Check-out', 'Valor', 'Cartão', 'Email', 'Telefone', 'Status', 'Pago em', 'Valor Pago'], ';');

        foreach ($reservations as $r) {
            fputcsv($out, [
                $r['code'],
                $r['confirmation_number'],
                $r['guest_name'],
                $r['checkin'],
                $r['checkout'],
                formatBRL((float)$r['rate_total']),
                $r['card_last4'],
                $r['email'],
                $r['phone'],
                $r['status'],
                $r['paid_at'] ?? '',
                $r['paid_amount'] ? formatBRL((float)$r['paid_amount']) : '',
            ], ';');
        }

        fclose($out);
        exit;
    }

    // ── Helpers ──

    private static function findCol(array $headers, array $names): int
    {
        foreach ($names as $name) {
            $idx = array_search(strtolower($name), $headers, true);
            if ($idx !== false) return (int)$idx;
        }
        return -1;
    }

    private static function getCol(array $row, int $index): string
    {
        return ($index >= 0 && isset($row[$index])) ? trim($row[$index]) : '';
    }

    private static function parseDate(string $str): string
    {
        $str = trim($str);
        if ($str === '') return '';

        // DD/MM/YYYY
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $str, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        // YYYY-MM-DD
        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $str)) {
            return $str;
        }

        $ts = strtotime($str);
        return $ts ? date('Y-m-d', $ts) : '';
    }

    private static function parseBRL(string $str): float
    {
        $str = preg_replace('/[^0-9.,]/', '', $str);
        // Brazilian: 2.029,41
        if (preg_match('/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/', $str)) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } else {
            $str = str_replace(',', '.', $str);
        }
        return (float)$str;
    }
}
