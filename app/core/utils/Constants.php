<?php

namespace App\Core\Utils;

/**
 * Static class for utility constants
 */
class Constants
{
    # Global statuses
    const STATUS_DEFAULT = 1;
    const STATUS_DELETED = -1;

    # User roles
    const ROLE_DEFAULT = 1;
    const ROLE_EDITOR = 2;
    const ROLE_ADMIN = 10;
    const ROLE_SUPER_ADMIN = 20;

    # Token types
    const TOKEN_EMAIL_REGISTRATION = 1;
    const TOKEN_RESET_PASSWORD = 2;

    # Session inactivity -> 1h
    const SESSION_TIMEOUT = 60;

    # Password recovery -> 24h
    const RESET_PASSWORD_TIMEOUT = 1440;
}
