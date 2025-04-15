<?php

declare(strict_types=1);

namespace App\Database;

use App\Exception\DatabaseException;

class DatabaseQueryBuilder implements QueryBuilderInterface
{
    public function buildSelect(string $table, array $conditions = []): array
    {
        $query = "SELECT * FROM `$table` WHERE 1=1";
        $types = '';
        $values = [];

        foreach ($conditions as $field => $value) {
            if ($value instanceof SqlExpressionInterface) {
                $query .= " AND `$field` " . $value->getExpression();
            } else {
                $query .= " AND `$field` = ?";
                $types .= $this->getTypeChar($value);
                $values[] = $this->normalizeValue($value);
            }
        }

        return [$query, $types, $values];
    }

    public function buildInsert(string $table, array $data): array
    {
        $fields = [];
        $placeholders = [];
        $types = '';
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = "`$field`";
            if ($value instanceof SqlExpressionInterface) {
                $placeholders[] = $value->getExpression();
            } else {
                $placeholders[] = '?';
                $types .= $this->getTypeChar($value);
                $values[] = $this->normalizeValue($value);
            }
        }

        $query = "INSERT INTO `$table` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        return [$query, $types, $values];
    }

    public function buildUpdate(string $table, int $id, array $data, array $conditions = []): array
    {
        $setClauses = [];
        $types = '';
        $values = [];

        foreach ($data as $field => $value) {
            if ($value instanceof SqlExpressionInterface) {
                $setClauses[] = "`$field` = " . $value->getExpression();
            } else {
                $setClauses[] = "`$field` = ?";
                $types .= $this->getTypeChar($value);
                $values[] = $this->normalizeValue($value);
            }
        }

        $query = "UPDATE `$table` SET " . implode(', ', $setClauses) . " WHERE `id` = ?";
        $types .= 'i';
        $values[] = $id;

        foreach ($conditions as $field => $value) {
            if ($value instanceof SqlExpressionInterface) {
                $query .= " AND `$field` " . $value->getExpression();
            } else {
                $query .= " AND `$field` = ?";
                $types .= $this->getTypeChar($value);
                $values[] = $this->normalizeValue($value);
            }
        }

        return [$query, $types, $values];
    }

    private function normalizeValue($value)
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_null($value)) {
            return null;
        }

            return $value;
    }


    private function getTypeChar($value): string
    {
        return match (true) {
            is_int($value) => 'i',
            is_float($value) => 'd',
            is_string($value) => 's',
            default => 's'
        };
    }
}
