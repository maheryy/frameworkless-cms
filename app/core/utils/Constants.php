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
    const ROLE_CONTRIBUTOR = 4;
    const ROLE_SUBSCRIBER = 5;

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
    const PERM_READ_MENU = 10;
    const PERM_CREATE_MENU = 11;
    const PERM_UPDATE_MENU = 12;
    const PERM_DELETE_MENU = 13;
    const PERM_READ_CUSTOMIZATION = 14;
    const PERM_UPDATE_CUSTOMIZATION = 15;
    const PERM_READ_SETTINGS = 16;
    const PERM_UPDATE_SETTINGS = 17;
    const PERM_READ_ROLE = 18;
    const PERM_CREATE_ROLE = 19;
    const PERM_UPDATE_ROLE = 20;

    # Menu types
    const MENU_LINKS = 1;
    const MENU_SOCIALS = 2;

    # Layout sections type
    const LS_FOOTER_TEXT = 1;
    const LS_FOOTER_LINKS = 2;
    const LS_FOOTER_CONTACT = 3;
    const LS_FOOTER_NEWSLETTER = 4;
    const LS_FOOTER_SOCIALS = 5;
    const LS_HEADER_MENU = 6;

    # General settings table keys
    const STG_TITLE = 'site_title';
    const STG_DESCRIPTION = 'site_description';
    const STG_EMAIL_ADMIN = 'email_admin';
    const STG_EMAIL_CONTACT = 'email_contact';
    const STG_ROLE = 'default_role';
    const STG_PUBLIC_SIGNUP = 'public_signup';
    const STG_SITE_LAYOUT = 'site_layout';
    const STG_HERO_DATA = 'hero_data';

    # Review statuses
    const REVIEW_PENDING = 0;
    const REVIEW_VALID = 1;
    const REVIEW_INVALID = -1;

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

    public static function getMenusTypes()
    {
        return [
            self::MENU_LINKS => 'Liens',
            self::MENU_SOCIALS => 'Réseaux sociaux',
        ];
    }

    public static function getSocialList()
    {
        return [
            'facebook' => [
                'label' => 'Facebook',
                'icon' => 'fab fa-facebook blue',
            ],
            'instagram' => [
                'label' => 'Instagram',
                'icon' => 'fab fa-instagram',
            ],
            'twitter' => [
                'label' => 'Twitter',
                'icon' => 'fab fa-twitter',
            ],
            'snapchat' => [
                'label' => 'Snapchat',
                'icon' => 'fab fa-snapchat',
            ],
            'discord' => [
                'label' => 'Discord',
                'icon' => 'fab fa-discord',
            ],
            'linkedin' => [
                'label' => 'LinkedIn',
                'icon' => 'fab fa-linkedin',
            ],
            'youtube' => [
                'label' => 'Youtube',
                'icon' => 'fab fa-youtube',
            ],
            'tumblr' => [
                'label' => 'Tumblr',
                'icon' => 'fab fa-tumblr',
            ]
        ];
    }
}
