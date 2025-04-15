<?php

declare(strict_types=1);

namespace App\Database;

interface SqlExpressionInterface
{
    public function getExpression(): string;
}
