<?php

declare(strict_types=1);

namespace App\Service;

class EmailService implements EmailServiceInterface
{
    public function sendWelcomeEmail(string $email): void
    {
        $subject = 'Dobro došli';
        $message = 'Dobro dosli na nas sajt. Potrebno je samo da potvrdite email adresu...';
        $headers = 'From: adm@kupujemprodajem.com';
        //mail($email, $subject, $message, $headers);

        
        // Simulacija slanja mejla
        echo "Simulacija slanja mejla:\n";
        echo "To: $email\n";
        echo "Subject: $subject\n";
        echo "Message: $message\n";
        echo "Headers: $headers\n";
    }
}