<?php

namespace App\Core\Utils;

/**
 * Static class for all database seeds
 * Seeds data methods must have the same name as the model name in order to call BaseRepository::runSeed()
 * Ex :
 *  - table name => my_table
 *  - model name => MyTable
 *  - seed data => Seeds::myTable()
 *
 */
class Seeds
{

    /**
     * List all of the seeds available in this class
     *
     * @return array
     */
    public static function getAvailableSeeds()
    {
        return [
            'role',
            'permission',
            'rolePermission'
        ];
    }

    public static function role()
    {
        return [
            Constants::ROLE_SUPER_ADMIN => ['name' => 'Super Administrateur'],
            Constants::ROLE_ADMIN => ['name' => 'Administrateur'],
            Constants::ROLE_EDITOR => ['name' => 'Editeur'],
            Constants::ROLE_TEST => ['name' => 'Test'],
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
        ];
    }

    public static function rolePermission()
    {
        return [
            1 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            2 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            3 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_USER],
            4 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_USER],
            5 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            6 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            7 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            8 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            9 => ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            10 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            11 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            12 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_USER],
            13 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_USER],
            14 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            15 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            16 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            17 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            18 => ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            19 => ['role_id' => Constants::ROLE_TEST, 'permission_id' => Constants::PERM_CREATE_USER],
            20 => ['role_id' => Constants::ROLE_TEST, 'permission_id' => Constants::PERM_READ_PAGE],
            21 => ['role_id' => Constants::ROLE_TEST, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
        ];
    }
}
