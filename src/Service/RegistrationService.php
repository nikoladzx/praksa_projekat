<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;
use App\Repository\UserLogRepositoryInterface;
use App\Validation\ValidatorInterface;
use App\Exception\ValidationException;
use App\Exception\DatabaseException;
use App\Database\SqlExpression;
use App\Validation\RegistrationValidator;
use App\Validation\Rules\EmailFormatRule;
use App\Validation\Rules\EmailNotExistsRule;
use App\Validation\Rules\MaxMindRule;
use App\Validation\Rules\PasswordLengthRule;
use App\Validation\Rules\PasswordsMatchRule;
use App\Validation\Validator;

class RegistrationService implements RegistrationServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private UserLogRepositoryInterface $userLogRepository;
    private EmailServiceInterface $emailService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserLogRepositoryInterface $userLogRepository,
        EmailServiceInterface $emailService
    ) {
        $this->userRepository = $userRepository;
        $this->userLogRepository = $userLogRepository;
        $this->emailService = $emailService;
    }

    public function register(string $email, string $password, string $password2, string $ipAddress): array
    {
        $data = [
            'email' => $email,
            'password' => $password,
            'password2' => $password2,
            'ip_address' => $ipAddress,
        ];

        $validator = new RegistrationValidator($this->userRepository);
        $errors = $validator->validate($data);

        if (!empty($errors)) {
            throw new ValidationException(implode(' ', $errors));
        }

        try {

             $users = $this->userRepository->findUsersPostedInLastDays(10);
             error_log(print_r($users, true));

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userRepository->register($email, $hashedPassword, $ipAddress);

            if ($userId) {
                $this->emailService->sendWelcomeEmail($email);
                $this->userLogRepository->logUserActivity($userId, 'registration');
                $_SESSION['userId'] = $userId;

                return ['success' => true, 'userId' => $userId];
            } else {
                throw new DatabaseException("Failed to create user.");
            }
        } catch (\mysqli_sql_exception $e) {
            throw new DatabaseException("Database error: " . $e->getMessage());
        }
    }
}
