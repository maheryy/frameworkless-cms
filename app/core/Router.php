<?php

namespace App\Core;

use Exception;

class Router {
    private $uri;
    private $routes = [];
    private $routesPath = './routes.yml';
    private $controller;
    private $slugs;
    private $action;

    public function __construct()
    {
        $this->loadRoutes();

        $uri = $this->getParsedUri();
        $action = $this->findAction($uri['slug']);
        
        $this->setController($action['controller']);
        $this->setAction($action['action']);
    }

    private function loadRoutes()
    {
        if( !file_exists($this->routesPath) ) {
            throw new Exception('File no exist');
        }

        $this->routes = yaml_parse_file($this->routesPath);
        if( empty($this->routes) ) {
            throw new Exception('no routes found in the file '. $this->routesPath);
        }
        
        foreach ($this->routes as $slug => $data) {
            $this->slugs[$data['controller']][$data['action']] = $slug;
        } 
    }

    private function setUri(string $uri) {
        $this->uri = $uri;
    }

    private function setController(string $controller)
    {
        $this->controller = ucfirst(mb_strtolower($controller));
    }

    private function setAction(string $action)
    {
        $this->action = mb_strtolower($action) .'Action';
    }

    public function getControllerPath() : string 
    {
        return PATH_CONTROLLERS. $this->controller .'.php';
    }

    public function getControllerNamespace() : string 
    {
        return 'App\Controllers\\'. $this->controller;
    }

    public function getAction() : string
    {
        return $this->action;
    }

    private function getParsedUri(): array
    {
        $uri = explode('?',$_SERVER['REQUEST_URI'])[0];
        $this->setUri($uri);
        $uri_exploded = explode('/', ltrim($uri, '/'));
        $slug = '/'. array_shift($uri_exploded);

        return [
            'slug' => $slug,
            'params' => $uri_exploded
        ];
    }

    private function findAction(string $slug) : array
    {
        if ( empty($this->routes[$slug])) {
            throw new Exception('this route doesnt exist '. $slug);
        }
        $controller = trim($this->routes[$slug]['controller']);
        $action = trim($this->routes[$slug]['action']);
        if ( empty($controller) ) {
            throw new Exception('no controller found at route'. $this->routes[$slug]);
        }
        if ( empty($action) ) {
            throw new Exception('no action found at route'. $this->routes[$slug]);
        }   

        return [
            'controller' => $controller,
            'action' => $action
        ];
    }

    private function callAction(string $class_name, string $action)
    {
        $class_path = $this->getControllerPath();
        if( file_exists($class_path) ) {
            include $class_path;

            if( class_exists($class_name, false) ) {
                $controller = new $class_name();

                if( method_exists($class_name, $action) ){
                    $controller->{$action}();
                } else {
                    die("L'action ". $action ." n'existe pas");
                }
            } else {
                die("La classe ". $class_name ." n'existe pas");
            }
        } else {
            die("Le fichier ". $class_path ." n'existe pas");
        }
    }

    public function run()
    {
        $class_name = $this->getControllerNamespace();
        $action = $this->getAction();

        $this->callAction($class_name, $action);
    }

    public function getRoute(string $controller, string $action)
    {
		if(empty($this->slugs[$controller]) && empty($this->slugs[$controller][$action]))
            throw new Exception("Aucune route associé à ".$controller." -> ".$action );
        
        return $this->slugs[$controller][$action];		
	}

    public static function redirect(string $path = '')
    {
        header('Location: http://localhost/'. $path);
    }
    
}