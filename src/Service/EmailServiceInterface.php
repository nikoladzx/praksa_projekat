<?php

namespace App\Service;

interface EmailServiceInterface
{
    public function sendWelcomeEmail(string $email): void;
}