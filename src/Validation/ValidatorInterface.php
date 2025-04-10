<?php

namespace App\Validation;

interface ValidatorInterface
{
    public function validate(array $data): array;
}