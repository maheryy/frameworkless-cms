<?php

namespace App\Core;

use App\Core\Utils\Formatter;
use App\Core\Utils\LayoutManager;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Core\Utils\UrlBuilder;

abstract class Controller
{
    protected array $view_data;
    protected Router $router;

    protected function __construct(array $options)
    {
        $this->view_data = [];
        $this->router = Router::getInstance();

        $this->initSession($options['require_auth'] ?? false);
    }

    /**
     * Render a view from any controller
     *
     * @param string $view
     * @param string $template
     */
    protected function render(string $view, string $template = 'back_office')
    {
        $this->view = new View($view, $template, $this->view_data);
    }

    /**
     * Set a view param (the variable of $key name can be used in the view)
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setParam(string $key, $value)
    {
        $this->view_data[$key] = $value;
    }

    /**
     * Set multiple view params (the variable of $key name can be used in the view)
     *
     * @param array $data
     */
    protected function setData(array $data)
    {
        $this->view_data = !empty($this->view_data)
            ? array_merge($this->view_data, $data)
            : $data;
    }

    /**
     * Send back a text message and terminate script execution
     *
     * @param string $message
     */
    protected function send(string $message)
    {
        echo $message;
        exit;
    }

    /**
     * Send back a JSON data and terminate script execution
     *
     * @param array $data
     */
    protected function sendJSON(array $data)
    {
        header_remove();
        header('Content-Type: application/json');
        $this->send(json_encode($data));
    }

    /**
     * Send back success response as JSON and terminate script execution
     *
     * @param string $message
     * @param array $data optional
     */
    protected function sendSuccess(string $message, array $data = null)
    {
        $this->sendJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send back error response as JSON and terminate script execution
     *
     * @param string $message
     * @param array $data optional
     */
    protected function sendError(string $message, array $data = null)
    {
        $this->sendJSON([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Set main parameters for toolbar/sidebar
     *
     */
    protected function setLayoutParams()
    {
        $layout = new LayoutManager();
        $sidebar_links = $layout->getSidebarLinks();
        $this->setParam('current_route', $this->router->getUri());
        $this->setParam('sidebar_links', $sidebar_links['main']);
        $this->setParam('link_settings', $sidebar_links['bottom']);
        $this->setParam('link_logout', UrlBuilder::makeUrl('Auth', 'logoutAction'));
        $this->setParam('sidebar', $layout->getSidebarPath());
        $this->setParam('toolbar', $layout->getToolbarPath());
    }

    /**
     * Set the section title
     *
     * @param string $title
     */
    protected function setContentTitle(string $title)
    {
        $this->setParam('content_title', $title);
    }

    /**
     * Set the page title - browser tab title
     *
     * @param string $title
     */
    protected function setPageTitle(string $title)
    {
        $this->setParam('meta_title', $title);
    }

    /**
     * Set CSRF Token
     */
    protected function setCSRFToken()
    {
        $this->setParam('csrf_token', Session::getCSRFToken());
    }

    /**
     * Start session and check if user is logged in
     */
    protected function initSession(bool $require_auth)
    {
        if (!Session::isActive()) {
            Session::start();
        }

        if (!$require_auth) return;

        if (!Session::isLoggedIn()) {
            $url_params = $_SERVER['REQUEST_URI'] !== '/' && $this->router->existRoute($_SERVER['REQUEST_URI']) ? ['redirect' => Formatter::encodeUrlQuery($_SERVER['REQUEST_URI'])] : [];
            $this->router->redirect(UrlBuilder::makeUrl('Auth', 'loginView', $url_params));
        }

        # Apply session timeout
        if (Session::hasExpired()) {
            $this->router->redirect(UrlBuilder::makeUrl('Auth', 'logoutAction', [
                'redirect' => $this->router->existRoute($_SERVER['REQUEST_URI']) ? Formatter::encodeUrlQuery($_SERVER['REQUEST_URI']) : '/',
                'timeout' => true
            ]));
        }
        if (!Session::isDev()) {
            Session::set('LAST_ACTIVE_TIME', time());
        }
    }

    /**
     * Validate CSRF Token for every form that requires user authentification
     */
    protected function validateCSRF()
    {
        $token = Request::header('X-CSRF-TOKEN');
        if (!$token || Session::getCSRFToken() !== $token) {
           $this->sendError('Accès refusé');
        }
        return true;
    }
}
