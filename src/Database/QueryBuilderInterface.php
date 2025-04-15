<?php

declare(strict_types=1);

namespace App\Database;

interface QueryBuilderInterface
{
    public function buildSelect(string $table, array $conditions = []): array;

    public function buildInsert(string $table, array $data): array;

    public function buildUpdate(string $table, int $id, array $data, array $conditions = []): array;
}
