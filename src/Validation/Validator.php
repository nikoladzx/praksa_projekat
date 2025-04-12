<?php

declare(strict_types=1);

namespace App\Validation;

use App\Validation\ValidationRuleInterface;

class Validator implements ValidatorInterface
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(array $data): array
    {
        $errors = [];
        foreach ($this->rules as $field => $fieldRules) {
            if (isset($data[$field])) {
                foreach ($fieldRules as $rule) {
                    if ($rule instanceof ValidationRuleInterface) {
                        $context = ['data' => $data];
                        if (!$rule->validate($data[$field], $context)) {
                            $errors[$field] = $rule->getMessage();
                            break;
                        }
                    }
                }
            }
        }
        return $errors;
    }
}