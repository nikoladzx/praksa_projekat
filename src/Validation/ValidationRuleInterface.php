<?php

declare(strict_types=1);

namespace App\Validation;

interface ValidationRuleInterface
{
    public function validate($value): bool;
    public function getMessage(): string;
}