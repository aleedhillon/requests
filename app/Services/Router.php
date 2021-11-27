<?php

namespace App\Services;

class Router
{
    protected $routes;

    const GET = 'GET';
    const POST = 'POST';

    protected $action = null;
    protected $routeParameter = null;

    protected function resolveUriWithMethod(string $uri, string $requestMethod)
    {

        $this->action = $this->routes[$requestMethod][$uri] ?? null;

        // if (isset($this->routes[$requestMethod])) {
        //     $uriParts = explode('/', $uri);
        //     $this->routeParameter = array_pop($uriParts);
        //     $uri = implode('/', $uriParts);

        //     $this->action = $this->routes[$requestMethod][$uri] ?? null;
        // }
    }

    public function resolve(string $uri, string $requestMethod)
    {
        $this->resolveUriWithMethod($uri, $requestMethod);

        if ($this->action) {
            $requestData = $this->getRequestData($requestMethod);
            if (is_array($this->action)) {
                $class = $this->action[0];
                $method = $this->action[1];
                $controller = new $class();
                return $controller->$method($requestData);
            } else {
                return (new $this->action)($requestData);
            }
        } else {
            return notFound();
        }
    }

    public function get(string $route, array|string $resolver)
    {
        $this->register(self::GET, $route, $resolver);

        return $this;
    }

    public function post(string $route, array|string $resolver)
    {
        $this->register(self::POST, $route, $resolver);

        return $this;
    }

    public function register(string $method, string $route, array|string $resolver)
    {
        $this->routes[$method][$route] = $resolver;

        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    protected function getRequestData(string $httpMethod)
    {
        if ($httpMethod == self::POST) {
            return $this->getPostRequestData();
        }

        if ($httpMethod == self::GET) {
            return $this->getGetRequestData();
        }

        return [];
    }

    protected function getGetRequestData()
    {
        $data = [];
        $headers = getallheaders();
        if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            $data = $_GET;
        }

        return $data;
    }

    protected function getPostRequestData()
    {
        $data = [];
        $headers = getallheaders();
        if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            $data = $_POST;
        }

        return $data;
    }
}
