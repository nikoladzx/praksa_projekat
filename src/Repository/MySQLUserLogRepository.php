<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\SqlExpression;
use App\Database\QueryBuilderInterface;
use App\Exception\DatabaseException;

class MySQLUserLogRepository extends AbstractRepository implements UserLogRepositoryInterface
{
    public function __construct(\mysqli $connection, QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($connection, $queryBuilder);
        $this->tableName = 'user_log';
    }
    public function logUserActivity(int $userId, string $action): ?int
    {
        $logData = [
            'action' => $action,
            'user_id' => $userId,
            'log_time' => new SqlExpression('NOW()'),
        ];

        return $this->create($logData);
    }
}