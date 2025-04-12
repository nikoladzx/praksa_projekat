<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Database/MySQLConnection.php';
require_once __DIR__ . '/src/Database/SqlExpression.php';
require_once __DIR__ . '/src/Repository/RepositoryInterface.php';
require_once __DIR__ . '/src/Repository/UserRepositoryInterface.php';
require_once __DIR__ . '/src/Repository/UserLogRepositoryInterface.php';
require_once __DIR__ . '/src/Repository/MySQLUserRepository.php';
require_once __DIR__ . '/src/Repository/MySQLUserLogRepository.php';
require_once __DIR__ . '/src/Service/EmailServiceInterface.php';
require_once __DIR__ . '/src/Service/EmailService.php';
require_once __DIR__ . '/src/Validation/ValidatorInterface.php';
require_once __DIR__ . '/src/Validation/Validator.php';
require_once __DIR__ . '/src/Validation/ValidationRuleInterface.php';
require_once __DIR__ . '/src/Validation/Rules/EmailFormatRule.php';
require_once __DIR__ . '/src/Validation/Rules/EmailNotExistsRule.php';
require_once __DIR__ . '/src/Validation/Rules/MaxMindRule.php';
require_once __DIR__ . '/src/Validation/Rules/PasswordLengthRule.php';
require_once __DIR__ . '/src/Validation/Rules/PasswordMatchRule.php';
require_once __DIR__ . '/src/Controller/RegistrationController.php';
require_once __DIR__ . '/src/Exception/ValidationException.php';
require_once __DIR__ . '/src/Exception/DatabaseException.php';
require_once __DIR__ . '/src/Routing/Router.php';
require_once __DIR__ . '/src/Container/ContainerInterface.php';
require_once __DIR__ . '/src/Container/Container.php';

use App\Database\MySQLConnection;
use App\Repository\MySQLUserRepository;
use App\Repository\UserRepositoryInterface;
use App\Repository\MySQLUserLogRepository;
use App\Repository\UserLogRepositoryInterface;
use App\Service\EmailServiceInterface;
use App\Service\EmailService;
use App\Container\Container;


define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'Password12#');
define('DB_NAME', 'praksa');

$container = new Container();

$container->set(PDO::class, function (Container $c) {
    $connection = new MySQLConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    return $connection->connect();
});

$container->set(UserRepositoryInterface::class, function (Container $c) {
    return new MySQLUserRepository($c->get(PDO::class));
});

$container->set(UserLogRepositoryInterface::class, function (Container $c) {
    return new MySQLUserLogRepository($c->get(PDO::class));
});

$container->set(EmailServiceInterface::class, function (Container $c) {
    return new EmailService();
});

return $container;