<?php

namespace App\Core;

use App\Controllers\Error;
use App\Core\Exceptions\ForbiddenAccessException;
use App\Core\Exceptions\HttpNotFoundException;
use App\Core\Exceptions\NotFoundException;
use Exception;

class Router
{
    private $uri;
    private $routes = [];
    private $routesPath = './routes.yml';
    private $controller;
    private $method;
    static private $slugs;

    /**
     * Run the route requested by the URI
     */
    public function run()
    {
        try {
            $this->loadRoutes();
            $this->execute();
        } catch (NotFoundException $e) {
            (new Error())->displayErrorNotFound($e);
        } catch (ForbiddenAccessException $e) {
            (new Error())->displayErrorNoAccess($e);
        } catch (HttpNotFoundException $e) {
            (new Error())->displayError404();
        } catch (Exception $e) {
            (new Error())->displayErrorDefault($e);
        }
    }

    /**
     * Parse routes.yml and set $slugs to retrieve a route
     * 
     * @return void
     */
    private function loadRoutes()
    {
        if (!file_exists($this->routesPath)) {
            throw new NotFoundException('File no exist');
        }

        $this->routes = yaml_parse_file($this->routesPath);
        if (empty($this->routes)) {
            throw new NotFoundException('no routes found in the file ' . $this->routesPath);
        }

        foreach ($this->routes as $slug => $data) {
            self::$slugs[$data['controller']][$data['method']] = $slug;
        }
    }

    /**
     * @param string $uri
     * 
     * @return void
     */
    private function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param string $controller
     * 
     * @return void
     */
    private function setController(string $controller)
    {
        $this->controller = ucfirst($controller);
    }

    /**
     * @param string $method
     * 
     * @return void
     */
    private function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    private function getControllerPath()
    {
        return PATH_CONTROLLERS . $this->controller . '.php';
    }

    /**
     * @return string
     */
    private function getControllerNamespace()
    {
        return 'App\Controllers\\' . $this->controller;
    }

    /**
     * @return string
     */
    private function getMethod()
    {
        return $this->method;
    }

    /**
     * Return the route and the queries of a given URI
     * 
     * @return array
     */
    private function getParsedUri()
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

    /**
     * Return the right controller & method associated with the route
     * 
     * @param string $slug 
     * 
     * @return array
     */
    private function findMethod(string $slug)
    {
        if (empty($this->routes[$slug])) {
            throw new HttpNotFoundException($slug);
        }
        $controller = trim($this->routes[$slug]['controller']);
        $method = trim($this->routes[$slug]['method']);

        if (empty($controller)) {
            throw new NotFoundException('no controller found at route' . $this->routes[$slug]);
        }
        if (empty($method)) {
            throw new NotFoundException('no method found at route' . $this->routes[$slug]);
        }

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }

    /**
     * Calls $method_name of a specified $class_name
     * 
     * @param string $class_name
     * @param string $method_name
     * 
     * @return void
     */
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
                    throw new NotFoundException("La méthode " . $method_name . " n'existe pas");
                }
            } else {
                throw new NotFoundException("La classe " . $class_name . " n'existe pas");
            }
        } else {
            throw new NotFoundException("Le fichier " . $class_path . " n'existe pas");
        }
    }

    /**
     * Parse URI and find the right method to execute
     * 
     */
    private function execute()
    {
        $uri = $this->getParsedUri();
        $method = $this->findMethod($uri['slug']);

        $this->setController($method['controller']);
        $this->setMethod($method['method']);

        $class_name = $this->getControllerNamespace();
        $method = $this->getMethod();

        $this->callMethod($class_name, $method);
    }

    /**
     * Return the current route in the URL
     * 
     * @return string
     */
    public static function getCurrentRoute()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $uri_exploded = explode('/', ltrim($uri, '/'));
        return '/' . array_shift($uri_exploded);
    }

    /**
     * Return the URI of a specified $method in a $controller
     * 
     * @param string $controller
     * @param string $method
     * 
     * @return string
     */
    public static function getRouteURI(string $controller, string $method)
    {
        if (empty(self::$slugs[$controller]) && empty(self::$slugs[$controller][$method]))
            throw new NotFoundException("Aucune route associé à " . $controller . " -> " . $method);

        return self::$slugs[$controller][$method];
    }

    /**
     * Redirect the user to $path
     * 
     * @param string $path
     * 
     * @return void
     */
    public static function redirect(string $path = '')
    {
        header('Location: http://localhost:' . SERVER_PORT . $path);
    }
}
