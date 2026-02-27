<?php

function getDatabase(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dir = APP_PATH . '/storage';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $dbPath = $dir . '/database.sqlite';
        $isNew = !file_exists($dbPath);

        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec("PRAGMA journal_mode=WAL");
        $pdo->exec("PRAGMA foreign_keys=ON");

        if ($isNew) {
            initDatabase($pdo);
        }
    }

    return $pdo;
}

function initDatabase(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code VARCHAR(6) UNIQUE NOT NULL,
            confirmation_number VARCHAR(50),
            guest_name VARCHAR(255) NOT NULL,
            checkin DATE NOT NULL,
            checkout DATE NOT NULL,
            rate_total DECIMAL(10,2) NOT NULL,
            card_last4 VARCHAR(4),
            email VARCHAR(255),
            phone VARCHAR(50),
            status VARCHAR(20) DEFAULT 'pending',
            pix_transaction_id VARCHAR(100),
            pix_status VARCHAR(20),
            payment_method VARCHAR(20),
            paid_amount DECIMAL(10,2),
            paid_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS settings (
            key VARCHAR(50) PRIMARY KEY,
            value TEXT NOT NULL
        );

        CREATE INDEX IF NOT EXISTS idx_reservations_code ON reservations(code);
        CREATE INDEX IF NOT EXISTS idx_reservations_status ON reservations(status);

        INSERT OR IGNORE INTO settings (key, value) VALUES ('card_button_active', '0');
    ");
}
