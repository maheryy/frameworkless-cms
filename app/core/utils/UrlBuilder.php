<?php

namespace App\Core\Utils;

use App\Core\Router;

class UrlBuilder
{

    /**
     * Return an absolute URL (ex: localhost:8080/login)
     * 
     * @param string $controller
     * @param string $method
     * @param array $params
     * 
     * @return string
     */
    public static function getAbsoluteUrl(string $controller, string $method, array $params = [])
    {
        return $_SERVER['HTTP_HOST'] . self::getUrl($controller, $method, $params);
    }

    /**
     * Return a method's URL (ex: /login)
     * 
     * @param string $controller
     * @param string $method
     * @param array $params
     * 
     * @return string
     */
    public static function getUrl(string $controller, string $method, array $params = [])
    {
        $route = Router::getRouteURI($controller, $method);
        $query_string = '';

        foreach ($params as $key => $value) {
            $query_string .= "$key=$value&";
        }

        if (!empty($query_string)) {
            $query_string = '?' . rtrim($query_string, '&');
        }

        return $route . $query_string;
    }
}
