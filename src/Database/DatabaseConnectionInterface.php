<?php

declare(strict_types=1);

namespace App\Database;

interface DatabaseConnectionInterface
{
    public function connect(): \mysqli;
    public function disconnect(): void;
}
