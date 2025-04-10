<?php

use App\Controller\RegistrationController;
use App\Database\MySQLConnection;
use App\Database\SqlExpression;
use App\Repository\MySQLUserRepository;
use App\Repository\MySQLUserLogRepository;
use App\Service\EmailService;
use App\Validation\Validator;
use App\Validation\Rules\EmailFormatRule;
use App\Validation\Rules\EmailNotExistsRule;
use App\Validation\Rules\MaxMindRule;
use App\Validation\Rules\PasswordLengthRule;
use App\Validation\Rules\PasswordsMatchRule;

session_start();

require_once __DIR__. '/../src/Controller/RegistrationController.php';
require_once __DIR__. '/../src/Database/MySQLConnection.php';
require_once __DIR__. '/../src/Database/SqlExpression.php';
require_once __DIR__. '/../src/Repository/RepositoryInterface.php';
require_once __DIR__. '/../src/Repository/UserRepositoryInterface.php';
require_once __DIR__. '/../src/Repository/UserLogRepositoryInterface.php';
require_once __DIR__. '/../src/Repository/MySQLUserRepository.php';
require_once __DIR__. '/../src/Repository/MySQLUserLogRepository.php';
require_once __DIR__. '/../src/Service/EmailServiceInterface.php';
require_once __DIR__. '/../src/Service/EmailService.php';
require_once __DIR__. '/../src/Validation/ValidatorInterface.php';
require_once __DIR__. '/../src/Validation/Validator.php';
require_once __DIR__. '/../src/Validation/ValidationRuleInterface.php';
require_once __DIR__. '/../src/Validation/Rules/EmailFormatRule.php';
require_once __DIR__. '/../src/Validation/Rules/EmailNotExistsRule.php';
require_once __DIR__. '/../src/Validation/Rules/MaxMindRule.php';
require_once __DIR__. '/../src/Validation/Rules/PasswordLengthRule.php';
require_once __DIR__. '/../src/Validation/Rules/PasswordMatchRule.php';
require_once __DIR__. '/../src/Exception/ValidationException.php';
require_once __DIR__. '/../src/Exception/DatabaseException.php';

header('Content-Type: application/json');

$email = $_POST['email']?? '';
$password = $_POST['password']?? '';
$password2 = $_POST['password2']?? '';
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$connection = new MySQLConnection("127.0.0.1", "root", "Password12#", "praksa");
$userRepository = new MySQLUserRepository($connection->connect());
$userLogRepository = new MySQLUserLogRepository($connection->connect());
$emailService = new EmailService();

if (empty($email) || empty($password) || empty($password2)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}
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
        new PasswordsMatchRule($_POST['password'] ?? '')
    ]
];

$validator = new Validator($rules);

$registrationController = new RegistrationController($validator, $userRepository, $userLogRepository, $emailService);

try {
    $result = $registrationController->register($email, $password, $password2, $ipAddress);
    echo json_encode($result);
} catch (\App\Exception\ValidationException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (\App\Exception\DatabaseException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}