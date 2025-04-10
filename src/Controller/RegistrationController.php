<?php

namespace App\Controller;

use App\Repository\UserRepositoryInterface;
use App\Repository\UserLogRepositoryInterface;
use App\Service\EmailServiceInterface;
use App\Validation\ValidatorInterface;
use App\Exception\ValidationException;
use App\Exception\DatabaseException;
use App\Database\SqlExpression;

class RegistrationController
{
    private ValidatorInterface $validator;
    private UserRepositoryInterface $userRepository;
    private UserLogRepositoryInterface $userLogRepository;
    private EmailServiceInterface $emailService;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository, UserLogRepositoryInterface $userLogRepository, EmailServiceInterface $emailService)
    {
        $this->validator = $validator;
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

        $errors = $this->validator->validate($data);

       
        if (!empty($errors)) {
            throw new ValidationException(implode(' ', $errors));
        }

        try {

            // test
            $conditions = [
                'posted' => new SqlExpression("> NOW() - INTERVAL 10 DAY")
            ];
            $users = $this->userRepository->findBy($conditions);
            error_log(print_r($users, true));
            // test end
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userRepository->create([
                'email' => $email,
                'password' => $hashedPassword,
                'registration_ip' => $ipAddress,
                'posted' => new SqlExpression('NOW()')
            ]);
            if ($userId) {
                $this->emailService->sendWelcomeEmail($email);
                $this->userLogRepository->create([
                    'action' => 'register',
                    'user_id' => $userId,
                    'log_time' => new SqlExpression('NOW()')
                ]);
                $_SESSION['userId'] = $userId;
                return ['success' => true, 'userId' => $userId];
            } else {
                throw new DatabaseException("Failed to create user.");
            }
        } catch (\mysqli_sql_exception $e) {
            throw new DatabaseException("Database error: ". $e->getMessage());
        }
    }
}