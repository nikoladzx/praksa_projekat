<?php

declare(strict_types=1);

namespace App\Service;

interface RegistrationServiceInterface
{
    public function register(string $email, string $password, string $password2, string $ipAddress): array;
}
