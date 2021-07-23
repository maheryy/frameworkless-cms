<?php

namespace App\Core\Utils;

class LayoutManager
{
    private array $permissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return string
     */
    public function getSidebarPath()
    {
        return PATH_TEMPLATES . 'layout/back_office_sidebar.php';
    }


    public function getSidebar()
    {
        return [
            'dashboard' => [
                'label' => 'Tableau de bord',
                'icon' => 'fas fa-columns',
                'route' => UrlBuilder::getUrl('dashboard'),
            ],
            'page' => [
                'label' => 'Pages',
                'icon' => 'far fa-file',
                'route' => UrlBuilder::getUrl('pages'),
                'sublinks' => [
                    'list_page' => [
                        'label' => 'Liste pages',
                        'route' => UrlBuilder::getUrl('pages'),
                        'is_visible' => true,
                    ],
                    'new_page' => [
                        'label' => 'Ajouter page',
                        'route' => UrlBuilder::getUrl('new-page'),
                        'is_visible' => $this->hasPermission(Constants::PERM_CREATE_PAGE)
                    ]
                ]
            ],
            'appearance' => [
                'label' => 'Apparance',
                'icon' => 'fas fa-palette',
                'route' => UrlBuilder::getUrl('menu'),
                'sublinks' => [
                    'menus' => [
                        'label' => 'Menus',
                        'route' => UrlBuilder::getUrl('menu'),
                        'is_visible' => $this->hasPermission(Constants::PERM_READ_MENU)
                    ],
                    'customization' => [
                        'label' => 'Personnalisation',
                        'route' => UrlBuilder::getUrl('customization'),
                        'is_visible' => $this->hasPermission(Constants::PERM_READ_CUSTOMIZATION)
                    ],
                ]
            ],
            'user' => [
                'label' => 'Utilisateurs',
                'icon' => 'fas fa-users',
                'route' => '/admin/users',
                'sublinks' => [
                    'list_user' => [
                        'label' => 'Liste utilisateurs',
                        'route' => UrlBuilder::getUrl('users'),
                        'is_visible' => true,
                    ],
                    'new_user' => [
                        'label' => 'Ajouter utilisateur',
                        'route' => UrlBuilder::getUrl('new-user'),
                        'is_visible' => $this->hasPermission(Constants::PERM_CREATE_USER)
                    ],
                    'roles' => [
                        'label' => 'Roles',
                        'route' => UrlBuilder::getUrl('role'),
                        'is_visible' => $this->hasPermission(Constants::PERM_READ_ROLE)
                    ],
                ]
            ]
        ];
    }

    public function getSettings()
    {
        return [
            'label' => 'Paramètres',
            'icon' => 'fas fa-cog',
            'route' => UrlBuilder::getUrl('settings'),
            'is_visible' => $this->hasPermission(Constants::PERM_READ_SETTINGS)
        ];
    }

    private function hasPermission(int $permission)
    {
        return in_array($permission, $this->permissions);
    }
}
