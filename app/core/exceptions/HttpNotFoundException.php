<?php

namespace App\Core\Exceptions;

class HttpNotFoundException extends \Exception
{

    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
