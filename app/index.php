<?php

namespace App;

use App\Core\Utils\ConstantManager;
use App\Core\Router;

require './core/utils/Autoloader.php';
Autoloader::register();

try {
    ConstantManager::loadConstants();
    
    $router = new Router();
    $router->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}

