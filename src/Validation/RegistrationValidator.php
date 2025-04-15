<?php

declare(strict_types=1);

namespace App\Validation;

use App\Repository\UserRepositoryInterface;
use App\Validation\Rules\EmailFormatRule;
use App\Validation\Rules\EmailNotExistsRule;
use App\Validation\Rules\MaxMindRule;
use App\Validation\Rules\PasswordLengthRule;
use App\Validation\Rules\PasswordsMatchRule;

class RegistrationValidator implements ValidatorInterface
{
    private UserRepositoryInterface $userRepository;
    
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function validate(array $data): array
    {
        $rules = [
            'email' => [
                new EmailFormatRule(),
                new EmailNotExistsRule($this->userRepository),
                new MaxMindRule($data['ip_address'] ?? '')
            ],
            'password' => [
                new PasswordLengthRule(8)
            ],
            'password2' => [
                new PasswordsMatchRule($data['password'] ?? '')
            ]
        ];
        
        $validator = new Validator($rules);
        return $validator->validate($data);
    }
}