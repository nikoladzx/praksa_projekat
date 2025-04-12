<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Validation\ValidationRuleInterface;

class MaxMindRule implements ValidationRuleInterface
{

    private string $ipaddress;

    public function __construct(string $ipaddress)
    {
        $this->ipaddress = $ipaddress;
    }
    public function validate($value): bool
    {
        // Simulacija MaxMind provere
        $suspiciousEmails = ['test@example.com', 'fraud@example.org'];
        $suspiciousIps = ['127.0.0.1'];

        if (in_array($value, $suspiciousEmails)) {
            return false;
        }

        if (in_array($this->ipaddress, $suspiciousIps)) {
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