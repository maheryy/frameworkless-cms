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

    # Session inactivity -> 1h
    const SESSION_TIMEOUT = 60;

    # Password recovery -> 24h
    const RESET_PASSWORD_TIMEOUT = 1440;
}
