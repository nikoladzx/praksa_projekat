<?php

namespace App\Repository;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?array;   
}