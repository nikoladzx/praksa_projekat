<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use App\Exception\DatabaseException;
use App\Service\RegistrationServiceInterface;

class RegistrationController
{
    private RegistrationServiceInterface $registrationService;

    public function __construct(RegistrationServiceInterface $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function register(string $email, string $password, string $password2, string $ipAddress): array
    {
        try {
            return $this->registrationService->register($email, $password, $password2, $ipAddress);
        } catch (ValidationException | DatabaseException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
