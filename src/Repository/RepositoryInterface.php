<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public function findById(int $id): ?array;
    
    public function findBy(array $conditions = []): array;

    public function create(array $data): ?int; 

    public function update(int $id, array $data, array $conditions = []): bool;
}