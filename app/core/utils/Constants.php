<?php

namespace App\Core\Utils;

/**
 * Static class for utility constants
 */
class Constants
{
    # Global statuses
    const STATUS_DELETED = -2;
    const STATUS_INACTIVE = -1;
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;
    const STATUS_PUBLISHED = 3;

    # Post types
    const POST_TYPE_PAGE = 1;
    const POST_TYPE_POST = 2;

    # User roles
    const ROLE_DEFAULT = 1;
    const ROLE_EDITOR = 2;
    const ROLE_ADMIN = 10;
    const ROLE_SUPER_ADMIN = 20;

    # Token types
    const TOKEN_EMAIL_CONFIRM = 1;
    const TOKEN_RESET_PASSWORD = 2;

    # Session inactivity -> 1h
    const SESSION_TIMEOUT = 60;

    # Password recovery -> 24h
    const RESET_PASSWORD_TIMEOUT = 1440;
    # Account confirmation -> 7j
    const EMAIL_CONFIRM_TIMEOUT = 10080;


    # Visibility types
    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PRIVATE = 2;


    public static function getRoles()
    {
        return [
            self::ROLE_DEFAULT => 'Normal',
            self::ROLE_EDITOR => 'Editeur',
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_SUPER_ADMIN => 'Super Administrateur',
        ];
    }

    public static function getPostStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_PUBLISHED => 'Publié',
        ];
    }

    public static function getVisibilityTypes()
    {
        return [
            self::VISIBILITY_PUBLIC => 'Publique',
            self::VISIBILITY_PRIVATE => 'Privé',
        ];
    }
}
