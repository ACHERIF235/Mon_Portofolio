<?php
class MessageModel
{
    public static function create(string $name, string $email, string $message): void
    {
        db_query(
            'INSERT INTO inbox_messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())',
            ['name' => $name, 'email' => $email, 'message' => $message]
        );
    }
}
