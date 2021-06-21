<?php

namespace App\Core\Utils;

use App\Core\Router;

class Session
{
    private bool $require_auth;

    public function __construct(bool $require_auth)
    {
        if (!$this->isActive()) {
            $this->start();
        }

        $this->require_auth = $require_auth;
    }

    public function init()
    {
        if (!$this->require_auth) {
            return false;
        }

        if (!$this->isLoggedIn()) {
            $router = Router::getInstance();
            $url_params = $router->getUri() !== '/' && $router->existRoute($router->getUri()) ? ['redirect' => Formatter::encodeUrlQuery($router->getFullUri())] : [];
            $router->redirect(UrlBuilder::makeUrl('User', 'loginView', $url_params));
        }

        # Apply session timeout
        if ($this->hasExpired()) {
            $router = Router::getInstance();
            $url_params = ['timeout' => true];
            if ($router->getUri() !== '/' && $router->existRoute($router->getUri())) {
                $url_params['redirect'] = Formatter::encodeUrlQuery($router->getFullUri());
            }
            $router->redirect(UrlBuilder::makeUrl('User', 'logoutAction', $url_params));
        }
        if (!$this->isDev()) {
            $this->set('LAST_ACTIVE_TIME', time());
        }

        return true;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->get('user_id');
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return (int) $this->get('user_role');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->get('is_admin');
    }

    /**
     * @return string
     */
    public function getCSRFToken()
    {
        return $this->get('csrf_token');
    }

    /**
     * Set multiple session variables
     * 
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param int|string $value
     * 
     */
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * 
     * @return string|null
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Get all session variables
     */
    public function getAll()
    {
        return $_SESSION;
    }

    /**
     * Remove a session variable
     * 
     * @param string $key
     */
    public function delete(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy an active session
     */
    public function stop()
    {
        if ($this->isActive()) {
            session_destroy();
        }
    }

    /**
     * Start or resume a session
     */
    public function start()
    {
        session_start();
    }

    /**
     * Session is currently active
     * 
     * @return bool
     */
    public function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Alias for isActive()
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Determines if the app is in developpement or in production
     *
     * @return bool
     */
    public function isDev()
    {
        return defined('APP_DEV') && APP_DEV;
    }

    /**
     * Session has expired
     * Only in production
     *
     * @return bool
     */
    public function hasExpired()
    {
        return !$this->isDev() && $this->get('LAST_ACTIVE_TIME')
            && time() - $this->get('LAST_ACTIVE_TIME') > Constants::SESSION_TIMEOUT * 60;
    }
}
