<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\SqlExpression;
use App\Database\QueryBuilderInterface;
use App\Exception\DatabaseException;

class MySQLUserLogRepository implements UserLogRepositoryInterface
{
    private \mysqli $connection;
    private QueryBuilderInterface $queryBuilder;
    private string $tableName = 'user_log';

    public function __construct(\mysqli $connection, QueryBuilderInterface $queryBuilder)
    {
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
    }

    public function findById(int $id): ?array
    {
        $logs = $this->findBy(['id' => $id]);
        return !empty($logs) ? $logs[0] : null;
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

    public function findBy(array $conditions = []): array
    {
        [$query, $types, $bindValues] = $this->queryBuilder->buildSelect($this->tableName, $conditions);

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error);
        }

        if (!empty($bindValues)) {
            $stmt->bind_param($types, ...$bindValues);
        }

        if (!$stmt->execute()) {
            throw new DatabaseException("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $logs = [];

        while ($log = $result->fetch_assoc()) {
            $logs[] = $log;
        }

        $stmt->close();
        return $logs;
    }

    public function create(array $data): ?int
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Data array cannot be empty for create operation.");
        }

        [$query, $types, $values] = $this->queryBuilder->buildInsert($this->tableName, $data);

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error . " SQL: " . $query);
        }

        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }

        if ($stmt->execute()) {
            $insertId = $this->connection->insert_id;
            $stmt->close();
            return $insertId;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new DatabaseException("Error executing statement: " . $error);
        }
    }

    public function update(int $id, array $data, array $conditions = []): bool
    {
        if (empty($data)) {
            return true;
        }

        [$query, $types, $values] = $this->queryBuilder->buildUpdate($this->tableName, $id, $data, $conditions);

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error . " SQL: " . $query);
        }

        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $success = $stmt->affected_rows >= 0;
            $stmt->close();
            return $success;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new DatabaseException("Error executing statement: " . $error);
        }
    }
}