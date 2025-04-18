<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array; 
    public function findUsersPostedInLastDays(int $days): ?array;
    public function register(string $email, string $hashedPassword, string $ipAddress): int;

    
}