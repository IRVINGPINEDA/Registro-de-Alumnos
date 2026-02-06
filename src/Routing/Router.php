<?php
declare(strict_types=1);

namespace App\Routing;

use App\Http\Response;

final class Router
{
    /** @var array<int,array{method:string,pattern:string,regex:string,vars:list<string>,handler:callable|array{class-string,string}} > */
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function dispatch(string $method, string $uri): Response
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($route['vars'] as $var) {
                if (isset($matches[$var])) {
                    $params[$var] = $matches[$var];
                }
            }

            return $this->invoke($route['handler'], $params);
        }

        return Response::html('<h1>404</h1><p>Ruta no encontrada.</p>', 404);
    }

    private function add(string $method, string $pattern, callable|array $handler): void
    {
        $normalizedPattern = rtrim($pattern, '/') ?: '/';

        $vars = [];
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $m) use (&$vars): string {
            $vars[] = $m[1];
            return '(?P<' . $m[1] . '>\\d+)';
        }, $normalizedPattern);

        $regexBody = $regex ?: '';
        if ($regexBody !== '/') {
            $regexBody = rtrim($regexBody, '/');
        }
        $regex = '#^' . $regexBody . '$#';
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'regex' => $regex,
            'vars' => $vars,
            'handler' => $handler,
        ];
    }

    /**
     * @param array<string,string> $params
     */
    private function invoke(callable|array $handler, array $params): Response
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            return $controller->$method($params);
        }
        return $handler($params);
    }
}
