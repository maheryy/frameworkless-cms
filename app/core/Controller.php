<?php

namespace App\Core;

use App\Core\Utils\LayoutManager;

abstract class Controller
{
    protected $template;
    protected $view_data = [];


    protected function __construct()
    {
    }

    /**
     * Render a view in from any controller
     * 
     * @param string $view
     * @param string $template
     * 
     * @return void
     */
    protected function render(string $view, string $template = 'default')
    {
        $this->view = new View($view, $template, $this->view_data);
    }

    /**
     * Set a view param (the variable of $key name can be used in the view)
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    protected function setParam(string $key, $value)
    {
        $this->view_data[$key] = $value;
    }

    /**
     * Set multiple view params (the variable of $key name can be used in the view)
     * 
     * @param array $data
     * 
     * @return void
     */
    protected function setData(array $data)
    {
        $this->view_data = !empty($this->view_data)
            ? array_merge($this->view_data, $data)
            : $data;
    }

    /**
     * Send back a text message
     * 
     * @param string $message
     * 
     * @return void
     */
    protected function send(string $message)
    {
        echo $message;
    }

    /**
     * Send back a JSON data 
     * 
     * @param array $data
     * 
     * @return void
     */
    protected function sendJSON(array $data)
    {
        header('Content-Type: application/json');
        $this->send(json_encode($data));
    }

    /**
     * Send back success response as JSON
     * 
     * @param string $message
     * @param array $data optional
     * 
     * @return void
     */
    protected function sendSuccess(string $message, array $data = [])
    {
        $this->sendJSON([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send back error response
     * 
     * @param string $message
     * 
     * @return void
     */
    protected function sendError(string $message)
    {
        http_response_code(400);
        $this->send($message);
    }

    /**
     * Set main parameters for toolbar/sidebar
     * 
     */
    protected function setLayoutParams()
    {
        $layout = new LayoutManager();
        $sidebar_links = $layout->getSidebarLinks();
        $this->setParam('current_route', Router::getCurrentRoute());
        $this->setParam('sidebar_links', $sidebar_links['main']);
        $this->setParam('link_settings', $sidebar_links['bottom']);
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
}
