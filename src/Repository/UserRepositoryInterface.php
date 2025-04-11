<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?array;   
}