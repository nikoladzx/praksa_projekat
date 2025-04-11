<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\SqlExpression;
use App\Exception\DatabaseException; 

class MySQLUserLogRepository implements UserLogRepositoryInterface
{
    private \mysqli $connection;

    public function __construct(\mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function findById(int $id): ?array
    {
        $conditions = ['id' => $id];
        $users = $this->findBy($conditions);

        return !empty($users) ? $users[0] : null;
    }

    public function findBy(array $conditions = []): array
    {
        $query = "SELECT * FROM `user_log` WHERE 1=1";
        $types = '';
        $bindValues = [];
        
        foreach ($conditions as $field => $value) {
            if ($value instanceof SqlExpression) {
                $query .= " AND `$field` " . $value->getExpression();
            } else {
                $query .= " AND `$field` = ?";
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $bindValues[] = $value;
            }
        }
        
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
        $users = [];
        
        while ($user = $result->fetch_assoc()) {
            $users[] = $user;
        }
        
        $stmt->close();
        return $users;
    }

    public function create(array $data): ?int
    {
        if (empty($data)) {
            throw new \InvalidArgumentException("Data array cannot be empty for create operation.");
        }

        $fields = [];
        $placeholders = [];
        $types = '';
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "`$field`";
            
            if ($value instanceof SqlExpression) {
                $placeholders[] = $value->getExpression();
            } else {
                $placeholders[] = '?';
                
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 's';
                    $value = ($value === null) ? null : ($value ? '1' : '0');
                }
                
                $values[] = $value;
            }
        }
        
        $sql = "INSERT INTO `user_log` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error . " SQL: " . $sql);
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

        $setClauses = [];
        $types = '';
        $values = [];
        
        foreach ($data as $field => $value) {
            if ($value instanceof SqlExpression) {
                $setClauses[] = "`$field` = " . $value->getExpression();
            } else {
                $setClauses[] = "`$field` = ?";
                
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 's';
                    $value = ($value === null) ? null : ($value ? '1' : '0');
                }
                
                $values[] = $value;
            }
        }
        
        $sql = "UPDATE `user_log` SET " . implode(', ', $setClauses) . " WHERE `id` = ?";
        $types .= 'i';
        $values[] = $id;
        
        $whereClauses = [];
        foreach ($conditions as $field => $value) {
            if ($value instanceof SqlExpression) {
                $whereClauses[] = "`$field` " . $value->getExpression();
            } else {
                $whereClauses[] = "`$field` = ?";
                
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 's';
                    $value = ($value === null) ? null : ($value ? '1' : '0');
                }
                
                $values[] = $value;
            }
        }
        
        if (!empty($whereClauses)) {
            $sql .= " AND " . implode(' AND ', $whereClauses);
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new DatabaseException("Error preparing statement: " . $this->connection->error . " SQL: " . $sql);
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
