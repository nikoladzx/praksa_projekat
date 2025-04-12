<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Validation\ValidationRuleInterface;
use App\Repository\UserRepositoryInterface;

class EmailNotExistsRule implements ValidationRuleInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value): bool
    {
        return $this->userRepository->findByEmail($value) === null;
    }

    public function getMessage(): string
    {
        return 'Email adresa već postoji u sistemu.';
    }
}