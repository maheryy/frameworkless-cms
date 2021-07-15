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
    
    # User roles
    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_EDITOR = 3;
    const ROLE_TEST = 4;

    # Permissions
    const PERM_READ_USER = 1;
    const PERM_CREATE_USER = 2;
    const PERM_UPDATE_USER = 3;
    const PERM_DELETE_USER = 4;
    const PERM_READ_PAGE = 5;
    const PERM_CREATE_PAGE = 6;
    const PERM_UPDATE_PAGE = 7;
    const PERM_PUBLISH_PAGE = 8;
    const PERM_DELETE_PAGE = 9;

    # Navigation types
    const NAVIGATION_HEADER = 1;
    const NAVIGATION_FOOTER = 2;

    # General settings table keys
    const STG_TITLE = 'site_title';
    const STG_DESCRIPTION = 'site_description';
    const STG_EMAIL_ADMIN = 'email_admin';
    const STG_EMAIL_CONTACT = 'email_contact';
    const STG_ROLE= 'default_role';
    const STG_PUBLIC_SIGNUP = 'public_signup';


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

    public static function getNavigationTypes()
    {
        return [
            self::NAVIGATION_HEADER => 'Header',
            self::NAVIGATION_FOOTER => 'Footer',
        ];
    }
}
