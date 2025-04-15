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
            if (!isset($data[$field])) {
                continue;
            }
            
            $fieldValue = $data[$field];
            $error = $this->validateField($fieldValue, $fieldRules);
            
            if ($error) {
                $errors[$field] = $error;
            }
        }
        
        return $errors;
    }
    
    private function validateField(mixed $value, array $fieldRules): ?string
    {
        foreach ($fieldRules as $rule) {
            if (!$rule instanceof ValidationRuleInterface) {
                continue;
            }
            
            if (!$rule->validate($value)) {
                return $rule->getMessage();
            }
        }
        
        return null;
    }
}