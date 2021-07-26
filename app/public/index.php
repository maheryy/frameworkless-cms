<?php

namespace App;

use App\Core\Utils\ConstantManager;
use App\Core\Router;

require __DIR__ . '/../core/utils/Autoloader.php';

Autoloader::register();
ConstantManager::loadConstants();
Router::getInstance()->run();
