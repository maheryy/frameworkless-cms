<?php

namespace App\Core\Utils;

use App\Core\Exceptions\NotFoundException;

class LayoutManager
{
    private $nav_path = '../routes/nav.yml';
    private $sidebar_links;
    private $toolbar_links;

    public function __construct()
    {
        $this->loadData();
    }

    /**
     * Parse nav.yml file to retrieve toolbar/sidebar links
     */
    private function loadData()
    {
        if (!file_exists($this->nav_path)) {
            throw new NotFoundException('File no exist');
        }

        $nav_data = yaml_parse_file($this->nav_path);


        $this->sidebar_links = [
            'main' => $nav_data['sidebar-main'],
            'bottom' => $nav_data['sidebar-bottom']
        ];

        $this->toolbar_links = [];
    }

    /**
     * @return string
     */
    public function getSidebarPath()
    {
        return PATH_TEMPLATES . 'layout/layout_sidebar.php';
    }

    /**
     * @return string
     */
    public function getToolbarPath()
    {
        return PATH_TEMPLATES . 'layout/layout_toolbar.php';
    }

    /**
     * @return array
     */
    public function getSidebarLinks()
    {
        return $this->sidebar_links;
    }

    /**
     * @return array
     */
    public function getToolbarinks()
    {
        return $this->toolbar_links;
    }
}
