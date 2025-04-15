<?php

declare(strict_types=1);

namespace App\Database;

class SqlExpression implements SqlExpressionInterface
{
    private string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }
}