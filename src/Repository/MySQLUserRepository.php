<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\QueryBuilderInterface;
use App\Exception\DatabaseException;
use App\Database\SqlExpression;

class MySQLUserRepository implements UserRepositoryInterface
{
    private \mysqli $connection;
    private QueryBuilderInterface $queryBuilder;
    private string $tableName = 'user';

    public function __construct(\mysqli $connection, QueryBuilderInterface $queryBuilder)
    {
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
    }

    public function findById(int $id): ?array
    {
        $users = $this->findBy(['id' => $id]);
        return !empty($users) ? $users[0] : null;
    }

    public function findByEmail(string $email): ?array
    {
        $users = $this->findBy(['email' => $email]);
        return !empty($users) ? $users[0] : null;
    }

    public function findUsersPostedInLastDays(int $days): ?array
    {
        $users = $this->findBy(['posted' => new SqlExpression("> NOW() - INTERVAL {$days} DAY")]);
        return $users;
    }

    public function findBy(array $conditions = []): array
    {
        [$query, $types, $values] = $this->queryBuilder->buildSelect($this->tableName, $conditions);

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error);
        }

        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }

        if (!$stmt->execute()) {
            throw new DatabaseException("Error executing statement: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $users = [];

        while ($user = $result->fetch_assoc()) {
            $users[] = $user;
        }

        $stmt->close();
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
