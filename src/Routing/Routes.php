<?php

declare(strict_types=1);

use App\Controller\RegistrationController;
use App\Validation\Rules\EmailFormatRule;
use App\Validation\Rules\EmailNotExistsRule;
use App\Validation\Rules\MaxMindRule;
use App\Validation\Rules\PasswordLengthRule;
use App\Validation\Rules\PasswordsMatchRule;
use App\Validation\Validator;
use App\Repository\MySQLUserRepository;
use App\Repository\MySQLUserLogRepository;
use App\Service\EmailService;
use App\Routing\Router;
use App\Exception\ValidationException;
use App\Exception\DatabaseException;

$router = new Router();

$router->post('/register', function () use ($container) {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        if (empty($email) || empty($password) || empty($password2)) {
            return ['success' => false, 'error' => 'Sva polja su obavezna'];
        }
        
        $userRepository = $container->get(MySQLUserRepository::class);

        $rules = [
            'email' => [
                new EmailFormatRule(),
                new EmailNotExistsRule($userRepository),
                new MaxMindRule($ipAddress)
            ],
            'password' => [
                new PasswordLengthRule(8)
            ],
            'password2' => [
                new PasswordsMatchRule($password)
            ]
        ];
        
        $validator = new Validator($rules);
        $userLogRepository = $container->get(MySQLUserLogRepository::class);
        $emailService = $container->get(EmailService::class);
        
        $controller = new RegistrationController(
            $validator,
            $userRepository,
            $userLogRepository,
            $emailService
        );
        
        return $controller->register($email, $password, $password2, $ipAddress);
        
    } catch (ValidationException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    } catch (DatabaseException $e) {
        error_log($e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred'];
    } catch (\Exception $e) {
        error_log($e->getMessage());
        return ['success' => false, 'error' => 'An unexpected error occurred'];
    }
});

$router->setErrorHandler(404, function($message) {
    return ['success' => false, 'error' => $message];
});

$router->setErrorHandler(405, function($message) {
    return ['success' => false, 'error' => 'Method Not Allowed'];
});