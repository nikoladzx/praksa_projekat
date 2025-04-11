<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Validation\ValidationRuleInterface;

class MaxMindRule implements ValidationRuleInterface
{
    public function validate($value, array $context = []): bool
    {
        // Simulacija MaxMind provere
        $suspiciousEmails = ['test@example.com', 'fraud@example.org'];
        $suspiciousIps = ['127.0.0.1'];

        if (in_array($value, $suspiciousEmails)) {
            return false;
        }

        if (isset($context['data']['ip_address']) && in_array($context['data']['ip_address'], $suspiciousIps)) {
            return false;
        }
        // Simulacija slučajnog neuspeha provere
        // U stvarnom scenariju, ovde bi bila poziv MaxMind API
        return rand(0, 10) > 0; 
    }

    public function getMessage(): string
    {
        return 'Registracija sa ovom email adresom ili IP adresom nije moguća.';
    }
}