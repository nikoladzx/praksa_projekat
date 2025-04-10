<?php

namespace App\Database;

class MySQLConnection
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private ?\mysqli $connection = null;

    public function __construct(string $host, string $username, string $password, string $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect(): \mysqli
    {
        if ($this->connection === null) {
            $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database);
            if ($this->connection->connect_error) {
                die("Connection failed: ". $this->connection->connect_error);
            }
        }
        return $this->connection;
    }

    public function disconnect(): void
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
