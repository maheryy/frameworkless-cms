<?php

namespace App\Core;

use App\Core\Exceptions\ForbiddenAccessException;
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
    protected array $view_data;

    protected function __construct(array $options)
    {
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->session = new Session($options['require_auth'] ?? false);
        $this->init($options);
    }

    /**
     * Initialize back office params, sessions and check database installation
     *
     * @param array $data
     * @return bool
     */
    private function init(array $options)
    {
        # First check database is ready before taking any actions
        if (!Database::isReady()) {
            if (Request::isPost()) $this->sendError("Une installation est nécessaire :" . UrlBuilder::makeAbsoluteUrl('Installer', 'installerView'));

            $this->router->redirect(UrlBuilder::makeUrl('Installer', 'installerView'));
        }
        # Check permission given in routes.yml
        if (isset($options['permission']) && !$this->hasPermission((int) $options['permission'])) {
            if(Request::isPost()) $this->sendError('Accès non autorisé');

            throw new ForbiddenAccessException('Accès non autorisé');
        }
        # Display back office sidebar for rendering only
        if (!empty($options['display_back_office'])) {
            $this->setLayoutParams();
        }
        # Content title editable in routes.yml
        if (isset($options['title'])) {
            $this->setContentTitle($options['title']);
        }
        # Default back office template
        $this->setTemplate('back_office');

        return $this->session->init();
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
            $data = array_merge($this->view_data, $data);
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


        $this->setParam('current_route', $this->router->getFullUri());
        $this->setParam('sidebar_links', $sidebar_links['main']);
        $this->setParam('link_settings', $sidebar_links['bottom']['settings']);
        $this->setParam('link_home', UrlBuilder::makeUrl('Home', 'dashboardView'));
        $this->setParam('link_logout', UrlBuilder::makeUrl('Auth', 'logoutAction'));
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

    /**
     * Get setting value from settings table
     *
     * @param int $setting_key
     * @return mixed
     */
    protected function getValue(string $setting_key)
    {
        if (!isset($this->settings)) {
            $this->settings = $this->repository->settings->findAll();
        }
        return $this->settings[$setting_key] ?? null;
    }
}
