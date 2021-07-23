<?php

namespace App\Core\Utils;

use App\Core\Router;

class UrlBuilder
{

    /**
     * Return an absolute URL (ex: localhost:8080/admin/login)
     *
     * @param string $controller
     * @param string $method
     * @param array $params
     *
     * @return string
     */
    public static function makeAbsoluteUrl(string $controller, string $method, array $params = [])
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . self::makeUrl($controller, $method, $params);
    }

    /**
     * Return a method's URL (ex: /admin/login)
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

        return '/admin' . $route . (!empty($params) ? '?' . http_build_query($params) : '');
    }

    /**
     * Return a back office URL
     *
     * @param string $url
     * @param array $params
     *
     * @return string
     */
    public static function getUrl(string $url, array $params = [])
    {
        return '/admin/' . $url . (!empty($params) ? '?' . http_build_query($params) : '');
    }
}
