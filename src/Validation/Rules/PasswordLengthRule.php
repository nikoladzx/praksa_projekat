<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Validation\ValidationRuleInterface;

class PasswordLengthRule implements ValidationRuleInterface
{
    private int $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function validate($value, array $context = []): bool
    {
        return is_string($value) && mb_strlen($value) >= $this->minLength;
    }

    public function getMessage(): string
    {
        return 'Lozinka mora imati najmanje '. $this->minLength. ' karaktera.';
    }
}