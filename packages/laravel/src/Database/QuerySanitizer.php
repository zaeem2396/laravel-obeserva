<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Database;

final class QuerySanitizer
{
    private const int MAX_STATEMENT_LENGTH = 4096;

    /**
     * @param  array<int, mixed>  $bindings
     */
    public function sanitize(string $sql, array $bindings = []): string
    {
        if ($bindings === []) {
            return $this->truncate($sql);
        }

        $statement = $sql;

        foreach ($bindings as $binding) {
            $replacement = $this->formatBinding($binding);
            $statement = preg_replace('/\?/', $replacement, (string) $statement, 1) ?? $statement;
        }

        return $this->truncate($statement);
    }

    private function formatBinding(mixed $binding): string
    {
        if ($binding === null) {
            return 'NULL';
        }

        if (is_bool($binding)) {
            return $binding ? '1' : '0';
        }

        if (is_int($binding) || is_float($binding)) {
            return (string) $binding;
        }

        if (is_string($binding)) {
            return "'".addslashes($binding)."'";
        }

        return "'?'";
    }

    private function truncate(string $sql): string
    {
        if (strlen($sql) <= self::MAX_STATEMENT_LENGTH) {
            return $sql;
        }

        return substr($sql, 0, self::MAX_STATEMENT_LENGTH).'...';
    }
}
