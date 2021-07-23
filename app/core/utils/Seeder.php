<?php

namespace App\Core\Utils;

/**
 * Static class for database seeds
 * Seeders data methods must have the same name as the model name in order to call BaseRepository::runSeed()
 * Ex :
 *  - table name => my_table
 *  - model name => MyTable
 *  - seed data => Seeder::myTable()
 *
 */
class Seeder
{

    /**
     * List all of the seeders available in this class
     *
     * @return array
     */
    public static function getAvailableSeeders()
    {
        return [
            'role',
            'permission',
            'rolePermission',
//            'settings',
        ];
    }

    public static function role()
    {
        return [
            Constants::ROLE_SUPER_ADMIN => ['name' => 'Super Administrateur'],
            Constants::ROLE_ADMIN => ['name' => 'Administrateur'],
            Constants::ROLE_EDITOR => ['name' => 'Editeur'],
            Constants::ROLE_CONTRIBUTOR => ['name' => 'Contributeur'],
            Constants::ROLE_SUBSCRIBER => ['name' => 'Abonné'],
        ];
    }

    public static function permission()
    {
        return [
            Constants::PERM_READ_USER => [
                'name' => 'Visibilité d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_CREATE_USER => [
                'name' => 'Création d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_UPDATE_USER => [
                'name' => 'Modification d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_DELETE_USER => [
                'name' => 'Suppression d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_READ_PAGE => [
                'name' => 'Visibilité d\'une page',
                'description' => ''
            ],
            Constants::PERM_CREATE_PAGE => [
                'name' => 'Création d\'une page',
                'description' => ''
            ],
            Constants::PERM_UPDATE_PAGE => [
                'name' => 'Modification d\'une page',
                'description' => ''
            ],
            Constants::PERM_PUBLISH_PAGE => [
                'name' => 'Publication d\'une page',
                'description' => ''
            ],
            Constants::PERM_DELETE_PAGE => [
                'name' => 'Suppression d\'une page',
                'description' => ''
            ],
            Constants::PERM_READ_MENU => [
                'name' => 'Visibilité des menus',
                'description' => ''
            ],
            Constants::PERM_CREATE_MENU => [
                'name' => 'Création d\'un menu',
                'description' => ''
            ],
            Constants::PERM_UPDATE_MENU => [
                'name' => 'Modification d\'un menu',
                'description' => ''
            ],
            Constants::PERM_DELETE_MENU => [
                'name' => 'Suppression d\'un menu',
                'description' => ''
            ],
            Constants::PERM_READ_CUSTOMIZATION => [
                'name' => 'Visibilité des personnalisations',
                'description' => ''
            ],
            Constants::PERM_UPDATE_CUSTOMIZATION => [
                'name' => 'Modification des personnalisations',
                'description' => ''
            ],
            Constants::PERM_READ_SETTINGS => [
                'name' => 'Visibilité des paramètres',
                'description' => ''
            ],
            Constants::PERM_UPDATE_SETTINGS => [
                'name' => 'Modification des paramètres',
                'description' => ''
            ],
            Constants::PERM_READ_ROLE => [
                'name' => 'Visibilité des rôles',
                'description' => ''
            ],
            Constants::PERM_CREATE_ROLE => [
                'name' => 'Création d\'un rôle',
                'description' => ''
            ],
            Constants::PERM_UPDATE_ROLE => [
                'name' => 'Modification d\'un rôle',
                'description' => ''
            ],
        ];
    }

    public static function rolePermission()
    {
        return [
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_SETTINGS],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_SETTINGS],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_ROLE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_ROLE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_ROLE],

            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_SETTINGS],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_SETTINGS],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_ROLE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_ROLE],

            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_ROLE],

            ['role_id' => Constants::ROLE_CONTRIBUTOR, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_CONTRIBUTOR, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_CONTRIBUTOR, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_CONTRIBUTOR, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],

            ['role_id' => Constants::ROLE_SUBSCRIBER, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_SUBSCRIBER, 'permission_id' => Constants::PERM_CREATE_PAGE],
        ];
    }

    public static function settings()
    {
        return [
            ['name' => Constants::STG_TITLE, 'value' => null],
            ['name' => Constants::STG_DESCRIPTION, 'value' => null],
            ['name' => Constants::STG_EMAIL_ADMIN, 'value' => null],
            ['name' => Constants::STG_EMAIL_CONTACT, 'value' => null],
            ['name' => Constants::STG_ROLE, 'value' => Constants::ROLE_CONTRIBUTOR],
            ['name' => Constants::STG_PUBLIC_SIGNUP, 'value' => 0],
            ['name' => Constants::STG_SITE_LAYOUT, 'value' => null],
            ['name' => Constants::STG_HERO_DATA, 'value' => null],
        ];
    }
}
