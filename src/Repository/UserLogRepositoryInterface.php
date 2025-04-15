<?php

declare(strict_types=1);

namespace App\Repository;

interface UserLogRepositoryInterface
{
    public function logUserActivity(int $userId, string $action): ?int;
}