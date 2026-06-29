<?php
class View
{
    public static function render(string $path, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $path . '.php';
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
