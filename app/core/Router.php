<?php

namespace App\Core;

use App\Controllers\Error;
use App\Core\Exceptions\ForbiddenAccessException;
use App\Core\Exceptions\HttpNotFoundException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Request;
use Exception;

class Router
{
    private static Router $instance;
    private string $uri;
    private string $fullUri;
    private string $routesPath;
    private string $controller;
    private string $method;
    private array $routes;
    private array $slugs;

    private function __construct()
    {
        $this->routesPath = '../routes/routes.yml';
        $this->fullUri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Singleton instance of the router
     *
     * @return Router
     *
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    /**
     * Run the route requested by the URI
     */
    public function run()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        try {
            if ($this->isBackOfficeUri($uri)) {
                $this->loadRoutes();
                $this->execute($this->getBackOfficeUri($uri));
            } else {
                $this->accessWebsite($uri);
            }
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
            $this->slugs[$data['controller']][$data['method']] = $slug;
        }
    }

    /**
     * @param string $uri
     */
    private function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param string $controller
     */
    private function setController(string $controller)
    {
        $this->controller = ucfirst($controller);
    }

    /**
     * @param string $method
     */
    private function setMethod(string $method)
    {
        $this->method = $method;
    }

    private function getControllerNamespace()
    {
        return 'App\Controllers\\' . $this->controller;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getFullUri()
    {
        return $this->fullUri;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Return the URI of a specified $method in a $controller
     *
     * @param string $controller
     * @param string $method
     *
     * @return string
     */
    public function getUriFromMethod(string $controller, string $method)
    {
        if (empty($this->slugs[$controller]) && empty($this->slugs[$controller][$method]))
            throw new NotFoundException("Aucune route associé à " . $controller . " -> " . $method);

        return $this->slugs[$controller][$method];
    }

    /**
     * Return the right controller & method associated with the route
     *
     * @return array
     */
    private function getRouteData()
    {
        if (!($route = $this->routes[$this->uri] ?? null)) {
            throw new HttpNotFoundException($this->uri);
        }
        $controller = trim($route['controller']);
        $method = trim($route['method']);

        if (empty($controller)) {
            throw new NotFoundException('no controller found at route' . $route);
        }
        if (empty($method)) {
            throw new NotFoundException('no method found at route' . $route);
        }

        # Get route options
        $options = [];
        if (count($route) > 2) {
            unset($route['controller'], $route['method']);
            $options = $route;
        }

        return [
            'controller' => $controller,
            'method' => $method,
            'options' => $options
        ];
    }

    /**
     * Calls $method_name of a specified $class_name then terminates the script
     *
     * @param string $class_name
     * @param string $method_name
     *
     * @return void
     */
    private function callMethod(string $class_name, string $method_name, array $options = [])
    {
        $class_path = PATH_CONTROLLERS . $this->controller . '.php';
        if (file_exists($class_path)) {
            include $class_path;

            if (class_exists($class_name, false)) {
                $controller = new $class_name($options);

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

        exit;
    }

    /**
     * Parse URI and find the right method to execute
     * @param string $uri
     *
     */
    private function execute(string $uri)
    {
        $this->setUri($uri);
        $route = $this->getRouteData();
        $this->setController($route['controller']);
        $this->setMethod($route['method']);

        $class_name = $this->getControllerNamespace();
        $method = $this->getMethod();

        $this->callMethod($class_name, $method, $route['options']);
    }

    private function accessWebsite(string $uri)
    {
        $this->setUri($uri);
        $this->setController('Website');
        $this->callMethod($this->getControllerNamespace(), Request::isPost() ? 'formAction' : 'display');
    }

    private function isBackOfficeUri(string $uri)
    {
        return preg_match("/^\/admin$|^\/admin\//", $uri);
    }


    private function getBackOfficeUri(string $uri)
    {
        return !empty($uri = substr($uri, 6)) ? $uri : '/' . $uri;
    }

    public function existRoute(string $uri)
    {
        return isset($this->routes[$uri]);
    }

    /**
     * Redirect the user to $path
     *
     * @param string $path
     *
     * @return void
     */
    public function redirect(string $path = '')
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $path);
        exit;
    }
}
