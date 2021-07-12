<?php

namespace App\Core\Utils;

use App\Core\BaseRepository;
use App\Core\Exceptions\NotFoundException;

class Repository
{
    public function __get($name)
    {
        $name = ucfirst($name);
        $class = NAMESPACE_MODELS . $name;
        $repository = NAMESPACE_REPOSITORIES . $name . "Repository";
        if (!class_exists($class) || !class_exists($repository)) {
            throw new NotFoundException("$class or $repository not found");
        }

        return new $repository(new $class());
    }

    public function __call($method, $args)
    {
        $method = ucfirst($method);
        $class = NAMESPACE_MODELS . $method;
        $repository = NAMESPACE_REPOSITORIES . $method . "Repository";

        if (!class_exists($repository)) {
            throw new NotFoundException("$repository not found");
        }

        return new $repository($args[0] ?? new $class());
    }
}
