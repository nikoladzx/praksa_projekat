<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\QueryBuilderInterface;
use App\Exception\DatabaseException;
use App\Database\SqlExpression;

class MySQLUserRepository extends AbstractRepository implements UserRepositoryInterface
{
    public function __construct(\mysqli $connection, QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($connection, $queryBuilder);
        $this->tableName = 'user';
    }

    public function findByEmail(string $email): ?array
    {
        $users = $this->findBy(['email' => $email]);
        return !empty($users) ? $users[0] : null;
    }

    public function findUsersPostedInLastDays(int $days): ?array
    {
        if ($days <= 0) {
            throw new \InvalidArgumentException("Days must be a positive integer.");
        }
        $users = $this->findBy(['posted' => new SqlExpression("> NOW() - INTERVAL {$days} DAY")]);
        return $users;
    }

    public function register(string $email, string $hashedPassword, string $ipAddress): int
    {
        return $this->create([
            'email' => $email,
            'password' => $hashedPassword,
            'registration_ip' => $ipAddress,
            'posted' => new SqlExpression('NOW()'),
        ]);
    }
}
