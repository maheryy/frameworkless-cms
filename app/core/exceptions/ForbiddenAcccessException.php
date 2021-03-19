<?php

namespace App\Core\Exceptions;

class ForbiddenAccessException extends \Exception
{

    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getMessageDetails()
    {
        return 'ForbiddenAccess : ' . $this->getMessage() . ' - thrown in ' . $this->getFile() . ' at line ' . $this->getLine();
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
