<?php

declare(strict_types=1);

namespace App\Validation;

interface ValidationRuleInterface
{
    public function validate($value, array $context): bool;
    public function getMessage(): string;
}