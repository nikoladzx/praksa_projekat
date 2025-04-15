<?php

declare(strict_types=1);

namespace App\Routing;

interface RouterInterface
{
    public function addRoute(string $method, string $path, callable $handler): void;

    public function get(string $path, callable $handler): void;

    public function post(string $path, callable $handler): void;

    public function handleRequest(string $method, string $path);
}
