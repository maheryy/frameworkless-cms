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
            1 => ['name' => 'Administrateur'],
            2 => ['name' => 'Super Administrateur'],
            3 => ['name' => 'Editeur'],
            4 => ['name' => 'Test'],
        ];
    }

    public static function permission()
    {
        return [
            1 => [
                'name' => 'Visibilité d\'un utilisateur',
                'reference' => 'user_read',
                'description' => ''
            ],
            2 => [
                'name' => 'Création d\'un utilisateur',
                'reference' => 'user_creation',
                'description' => ''
            ],
            3 => [
                'name' => 'Modification d\'un utilisateur',
                'reference' => 'user_edit',
                'description' => ''
            ],
            4 => [
                'name' => 'Suppression d\'un utilisateur',
                'reference' => 'user_delete',
                'description' => ''
            ],
            5 => [
                'name' => 'Visibilité d\'une page',
                'reference' => 'page_read',
                'description' => ''
            ],
            6 => [
                'name' => 'Création d\'une page',
                'reference' => 'page_creation',
                'description' => ''
            ],
            7 => [
                'name' => 'Modification d\'une page',
                'reference' => 'page_edit',
                'description' => ''
            ],
            8 => [
                'name' => 'Publication d\'une page',
                'reference' => 'page_delete',
                'description' => ''
            ],
        ];
    }

    public static function rolePermission()
    {
        return [
            1 => ['role_id' => 1, 'permission_id' => 1],
            2 => ['role_id' => 1, 'permission_id' => 2],
            3 => ['role_id' => 1, 'permission_id' => 3],
            4 => ['role_id' => 1, 'permission_id' => 4],
            5 => ['role_id' => 1, 'permission_id' => 5],
            6 => ['role_id' => 1, 'permission_id' => 6],
            7 => ['role_id' => 1, 'permission_id' => 7],
            8 => ['role_id' => 1, 'permission_id' => 8],
            9 => ['role_id' => 2, 'permission_id' => 1],
            10 => ['role_id' => 2, 'permission_id' => 2],
            11 => ['role_id' => 2, 'permission_id' => 3],
            12 => ['role_id' => 2, 'permission_id' => 4],
            13 => ['role_id' => 2, 'permission_id' => 5],
            14 => ['role_id' => 2, 'permission_id' => 6],
            15 => ['role_id' => 2, 'permission_id' => 7],
            16 => ['role_id' => 2, 'permission_id' => 8],
            17 => ['role_id' => 4, 'permission_id' => 2],
            18 => ['role_id' => 4, 'permission_id' => 5],
            19 => ['role_id' => 4, 'permission_id' => 8],
        ];
    }
}
