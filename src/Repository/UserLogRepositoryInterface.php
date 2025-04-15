<?php

declare(strict_types=1);

namespace App\Repository;

interface UserLogRepositoryInterface extends RepositoryInterface
{
    public function logUserActivity(int $userId, string $action): ?int;
}