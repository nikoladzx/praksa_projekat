<?php

declare(strict_types=1);

namespace App\Container;

interface ContainerInterface
{
    public function set(string $id, callable $callable, bool $shared = true): void;

    public function get(string $id): mixed;

    public function has(string $id): bool;
}
