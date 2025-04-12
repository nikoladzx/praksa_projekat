<?php

declare(strict_types=1);

namespace App\Container;
use Exception;


class Container implements ContainerInterface
{
    private array $definitions = [];
    private array $instances = [];


    public function set(string $id, callable $callable, bool $shared = true): void
    {
        $this->definitions[$id] = ['callable' => $callable, 'shared' => $shared];
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!$this->has($id)) {
            throw new Exception("Service with ID '{$id}' is not defined.");
        }

        $definition = $this->definitions[$id];
        $instance = call_user_func($definition['callable'], $this);

        if ($definition['shared']) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
}