<?php

namespace App\Validation;

interface ValidationRuleInterface
{
    public function validate($value, array $context): bool;
    public function getMessage(): string;
}