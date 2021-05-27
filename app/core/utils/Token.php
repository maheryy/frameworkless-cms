<?php

namespace App\Core\Utils;

class Token
{
    private $token;

    public function __construct(string $token = null)
    {
        $this->token = $token;
    }

    public function generate(int $length = 32)
    {
        $this->token = random_bytes($length);
        return $this;
    }

    public function decode()
    {
        $this->token = $this->getDecoded();
        return $this;
    }

    public function encode()
    {
        $this->token = $this->getEncoded();
        return $this;
    }

    public function getHash()
    {
        return hash('sha256', $this->token);
    }

    public function getEncoded()
    {
        return bin2hex($this->token);
    }

    public function getDecoded()
    {
        return hex2bin($this->token);
    }

    public function get()
    {
        return $this->token;
    }

    public function equals(string $hash)
    {
        return hash_equals($this->getHash(), $hash);
    }

    public function __toString()
    {
        return $this->token;
    }
}
