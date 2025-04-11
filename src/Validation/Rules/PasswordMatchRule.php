<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Validation\ValidationRuleInterface;

class PasswordsMatchRule implements ValidationRuleInterface
{
    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function validate($value, array $context = []): bool
    {
        return $value === $this->password;
    }

    public function getMessage(): string
    {
        return 'Lozinke se ne poklapaju.';
    }
}