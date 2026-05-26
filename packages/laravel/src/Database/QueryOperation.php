<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Database;

final class QueryOperation
{
    public static function fromSql(string $sql): string
    {
        $parts = explode(' ', trim($sql), 2);
        $token = strtoupper(trim($parts[0]));

        return match ($token) {
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP', 'TRUNCATE' => strtolower($token),
            default => 'other',
        };
    }
}
