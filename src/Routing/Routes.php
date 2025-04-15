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
use App\Repository\UserLogRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Routing\RouterInterface;
use App\Service\EmailServiceInterface;
use App\Service\RegistrationServiceInterface;

$router = $container->get(RouterInterface::class);

$router->post('/register', function () use ($container) {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        if (empty($email) || empty($password) || empty($password2)) {
            return ['success' => false, 'error' => 'Sva polja su obavezna'];
        }

        $registrationService = $container->get(RegistrationServiceInterface::class);
        
        $controller = new RegistrationController(
            $registrationService
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
