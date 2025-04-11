<?php

declare(strict_types=1);

namespace App\Routing;

class Router
{
    private array $routes = [];
    private array $errorHandlers = [];
    
    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }
    
    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function setErrorHandler(int $code, callable $handler): void
    {
        $this->errorHandlers[$code] = $handler;
    }
    
    public function handleRequest(string $method, string $path)
    {
        if (!isset($this->routes[$method][$path])) {
            return $this->handleError(404, 'Not Found');
        }
        
        try {
            return call_user_func($this->routes[$method][$path]);
        } catch (\App\Exception\ValidationException $e) {
            return $this->handleError(400, $e->getMessage());
        } catch (\App\Exception\DatabaseException $e) {
            error_log("Database Error: " . $e->getMessage());
            return $this->handleError(500, 'Došlo je do greške u bazi podataka.');
        } catch (\Exception $e) {
            error_log("General Error: " . $e->getMessage());
            return $this->handleError(500, 'Došlo je do neočekivane greške.');
        }
    }
    
    private function handleError(int $code, string $message): array
    {
        http_response_code($code);
        
        if (isset($this->errorHandlers[$code])) {
            return call_user_func($this->errorHandlers[$code], $message);
        }
        
        return ['success' => false, 'error' => $message];
    }
}