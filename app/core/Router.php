<?php

namespace App\Core;

use Exception;

class Router
{
    private $uri;
    private $routes = [];
    private $routesPath = './routes.yml';
    private $controller;
    private $method;
    static private $slugs;

    public function __construct()
    {
        $this->loadRoutes();

        $uri = $this->getParsedUri();
        $method = $this->findMethod($uri['slug']);


        $this->setController($method['controller']);
        $this->setMethod($method['method']);
    }

    private function loadRoutes()
    {
        if (!file_exists($this->routesPath)) {
            throw new Exception('File no exist');
        }

        $this->routes = yaml_parse_file($this->routesPath);
        if (empty($this->routes)) {
            throw new Exception('no routes found in the file ' . $this->routesPath);
        }

        foreach ($this->routes as $slug => $data) {
            self::$slugs[$data['controller']][$data['method']] = $slug;
        }
    }

    private function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    private function setController(string $controller)
    {
        $this->controller = ucfirst($controller);
    }

    private function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function getControllerPath(): string
    {
        return PATH_CONTROLLERS . $this->controller . '.php';
    }

    public function getControllerNamespace(): string
    {
        return 'App\Controllers\\' . $this->controller;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    private function getParsedUri(): array
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->setUri($uri);
        $uri_exploded = explode('/', ltrim($uri, '/'));
        $slug = '/' . array_shift($uri_exploded);

        return [
            'slug' => $slug,
            'params' => $uri_exploded
        ];
    }

    private function findMethod(string $slug): array
    {
        if (empty($this->routes[$slug])) {
            throw new Exception('this route doesnt exist ' . $slug);
        }
        $controller = trim($this->routes[$slug]['controller']);
        $method = trim($this->routes[$slug]['method']);

        if (empty($controller)) {
            throw new Exception('no controller found at route' . $this->routes[$slug]);
        }
        if (empty($method)) {
            throw new Exception('no method found at route' . $this->routes[$slug]);
        }

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }

    private function callMethod(string $class_name, string $method_name)
    {
        $class_path = $this->getControllerPath();
        if (file_exists($class_path)) {
            include $class_path;

            if (class_exists($class_name, false)) {
                $controller = new $class_name();

                if (method_exists($class_name, $method_name)) {
                    $controller->{$method_name}();
                } else {
                    die("La méthode " . $method_name . " n'existe pas");
                }
            } else {
                die("La classe " . $class_name . " n'existe pas");
            }
        } else {
            die("Le fichier " . $class_path . " n'existe pas");
        }
    }

    public function run()
    {
        $class_name = $this->getControllerNamespace();
        $method = $this->getMethod();

        $this->callMethod($class_name, $method);
    }

    public static function getRoute(string $controller, string $method): string
    {
        if (empty(self::$slugs[$controller]) && empty(self::$slugs[$controller][$method]))
            throw new Exception("Aucune route associé à " . $controller . " -> " . $method);

        return self::$slugs[$controller][$method];
    }

    public static function redirect(string $path = '')
    {
        header('Location: http://localhost/' . $path);
    }
}
