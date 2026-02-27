<?php

class Setting
{
    public static function get(string $key, string $default = ''): string
    {
        $db = getDatabase();
        $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : $default;
    }

    public static function set(string $key, string $value): void
    {
        $db = getDatabase();
        $stmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value");
        $stmt->execute([$key, $value]);
    }

    public static function toggle(string $key): string
    {
        $current = self::get($key, '0');
        $new = $current === '1' ? '0' : '1';
        self::set($key, $new);
        return $new;
    }
}
