<?php

namespace App\Core\Utils;

use App\Core\Exceptions\NotFoundException;

class LayoutManager
{
    private $nav_path = '../routes/nav.yml';
    private $sidebar_links;

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
    }

    /**
     * @return string
     */
    public function getSidebarPath()
    {
        return PATH_TEMPLATES . 'layout/back_office_sidebar.php';
    }

    /**
     * @return array
     */
    public function getSidebarLinks()
    {
        return $this->sidebar_links;
    }
}
