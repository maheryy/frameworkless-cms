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
                'icon' => 'fas fa-home',
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
                    ],
                    'new_page' => [
                        'label' => 'Ajouter page',
                        'route' => UrlBuilder::getUrl('new-page'),
                        'hidden' => !$this->hasPermission(Constants::PERM_CREATE_PAGE)
                    ]
                ]
            ],
            'newsletter' => [
                'label' => 'Newsletter',
                'icon' => 'fas fa-newspaper',
                'route' => UrlBuilder::getUrl('newsletters'),
                'sublinks' => [
                    'list_newsletter' => [
                        'label' => 'Liste newsletter',
                        'route' => UrlBuilder::getUrl('newsletters'),
                    ],
                    'new_newsletter' => [
                        'label' => 'Ajouter newsletter',
                        'route' => UrlBuilder::getUrl('new-newsletter'),
                        'hidden' => !$this->hasPermission(Constants::PERM_CREATE_NEWSLETTER)
                    ],
                ]
            ],
            'reviews' => [
                'label' => 'Avis',
                'icon' => 'fas fa-star',
                'route' => UrlBuilder::getUrl('reviews'),
                'hidden' => !$this->hasPermission(Constants::PERM_READ_REVIEW)
            ],
            'appearance' => [
                'label' => 'Apparance',
                'icon' => 'fas fa-palette',
                'route' => UrlBuilder::getUrl('menu'),
                'sublinks' => [
                    'menus' => [
                        'label' => 'Menus',
                        'route' => UrlBuilder::getUrl('menu'),
                        'hidden' => !$this->hasPermission(Constants::PERM_READ_MENU)
                    ],
                    'customization' => [
                        'label' => 'Personnalisation',
                        'route' => UrlBuilder::getUrl('customization'),
                        'hidden' => !$this->hasPermission(Constants::PERM_READ_CUSTOMIZATION)
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
                    ],
                    'new_user' => [
                        'label' => 'Ajouter utilisateur',
                        'route' => UrlBuilder::getUrl('new-user'),
                        'hidden' => !$this->hasPermission(Constants::PERM_CREATE_USER)
                    ],
                    'roles' => [
                        'label' => 'Roles',
                        'route' => UrlBuilder::getUrl('role'),
                        'hidden' => !$this->hasPermission(Constants::PERM_READ_ROLE)
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
            'hidden' => $this->hasPermission(Constants::PERM_READ_SETTINGS)
        ];
    }

    private function hasPermission(int $permission)
    {
        return in_array($permission, $this->permissions);
    }
}
