<?php
class SettingModel
{
    public static function get(string $key, $default = null)
    {
        $row = db_fetch('SELECT value FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
        return $row['value'] ?? $default;
    }

    public static function all(): array
    {
        $rows = db_fetchAll('SELECT `key`, `value` FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    public static function save(string $key, string $value): void
    {
        if (db_fetch('SELECT 1 FROM settings WHERE `key` = :key', ['key' => $key])) {
            db_query('UPDATE settings SET value = :value WHERE `key` = :key', ['value' => $value, 'key' => $key]);
        } else {
            db_query('INSERT INTO settings (`key`, `value`) VALUES (:key, :value)', ['key' => $key, 'value' => $value]);
        }
    }
}
