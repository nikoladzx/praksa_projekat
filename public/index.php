<?php

declare(strict_types=1);

use App\Controller\RegistrationController;
use App\Validation\Validator;
use App\Validation\Rules\EmailFormatRule;
use App\Validation\Rules\EmailNotExistsRule;
use App\Validation\Rules\MaxMindRule;
use App\Validation\Rules\PasswordLengthRule;
use App\Validation\Rules\PasswordsMatchRule;
use App\Repository\MySQLUserRepository;
use App\Repository\MySQLUserLogRepository;
use App\Service\EmailService;
use App\Routing\Router;

session_start();

$container = require_once __DIR__ . '/../Bootstrap.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routePath = parse_url($requestUri, PHP_URL_PATH);

$router = new Router();

$router->post('/register', function () use ($container) {
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
    
    $registrationController = new RegistrationController(
        $validator,
        $userRepository,
        $userLogRepository,
        $emailService
    );
    
    return $registrationController->register($email, $password, $password2, $ipAddress);
});

$router->setErrorHandler(404, function($message) {
    return ['success' => false, 'error' => $message];
});

$router->setErrorHandler(405, function($message) {
    return ['success' => false, 'error' => 'Method Not Allowed'];
});

header('Content-Type: application/json');
$result = $router->handleRequest($requestMethod, $routePath);
echo json_encode($result);