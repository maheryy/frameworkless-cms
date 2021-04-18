<?php

namespace App;

use App\Core\Utils\ConstantManager;
use App\Core\Router;
use Exception;

require '../core/utils/Autoloader.php';
Autoloader::register();

try {
    ConstantManager::loadConstants();
} catch (Exception $e) {
    die('Failed to load constants : ' . $e->getMessage());
}

Router::getInstance()->run();
