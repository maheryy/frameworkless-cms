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
    public static function makeAbsoluteUrl(string $controller, string $method, array $params = [])
    {
        return $_SERVER['HTTP_HOST'] . self::makeUrl($controller, $method, $params);
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
    public static function makeUrl(string $controller, string $method, array $params = [])
    {
        $route = Router::getInstance()->getUriFromMethod($controller, $method);
        $query_string = '';

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query_string .= "$key=$value&";
            }
            $query_string = '?' . rtrim($query_string, '&');
        }

        return $route . $query_string;
    }
}
