<?php

namespace App\Core;

use App\Core\Utils\Formatter;
use App\Core\Utils\LayoutManager;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Core\Utils\UrlBuilder;

abstract class Controller
{
    protected Router $router;
    protected Request $request;
    protected Repository $repository;
    protected Session $session;
    protected array $settings;
    protected string $template;

    protected function __construct(array $options)
    {
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->session = new Session($options['require_auth'] ?? false);
        $this->settings = $this->repository->settings->findAll();

        # Default back office template
        $this->setTemplate('back_office');

        if ($this->session->init()) {
            $this->setLayoutParams();
        }

        if (isset($options['title'])) {
            $this->setContentTitle($options['title']);
        }
    }

    /**
     * Render a view from any controller
     *
     * @param string $view
     * @param array $data
     */
    protected function render(string $view, array $data = [])
    {
        if (!empty($this->view_data)) {
            $data = !empty($data) ? array_merge($this->view_data, $data) : $this->view_data;
        }
        return new View($view, $this->template, $data);
    }

    /**
     * Render a view without template
     *
     * @param string $view
     * @param array $data
     */
    protected function renderViewOnly(string $view, array $data = [])
    {
        if (!empty($this->view_data)) {
            $data = !empty($data) ? array_merge($this->view_data, $data) : $this->view_data;
        }
        return new View($view, null, $data);
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
     * Set a the main application template
     *
     * @param string $template
     */
    protected function setTemplate(string $template)
    {
        $this->template = $template;
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
     * Set main parameters for sidebar
     *
     */
    protected function setLayoutParams()
    {
        $layout = new LayoutManager();
        $sidebar_links = $layout->getSidebarLinks();
        $user_link = UrlBuilder::makeUrl('User', 'userView', ['id' => $this->session->getUserId()]);

        # Set custom link to existing nav-link
        $sidebar_links['main']['user']['sub-links']['current_user']['route'] = $user_link;

        $this->setParam('current_route', $this->router->getFullUri());
        $this->setParam('sidebar_links', $sidebar_links['main']);
        $this->setParam('link_settings', $sidebar_links['bottom']['settings']);
        $this->setParam('link_home', UrlBuilder::makeUrl('Home', 'dashboardView'));
        $this->setParam('link_logout', UrlBuilder::makeUrl('User', 'logoutAction'));
        $this->setParam('link_website', '/');
        $this->setParam('link_user', $user_link);
        $this->setParam('sidebar', $layout->getSidebarPath());
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
        $this->setParam('csrf_token', $this->session->getCSRFToken());
    }

    /**
     * Validate CSRF Token for every form that requires user authentification
     */
    protected function validateCSRF()
    {
        $token = $this->request->header('X-Csrf-Token');
        if (!$token || $this->session->getCSRFToken() !== $token) {
            $this->sendError('Accès refusé');
        }
        return true;
    }

    /**
     * Check permission
     *
     * @param int $permission
     * @return bool
     */
    protected function hasPermission(int $permission)
    {
        return in_array($permission, $this->session->get('permissions'));
    }
}
