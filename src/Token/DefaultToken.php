<?php

namespace Bunq\Token;

final class DefaultToken implements Token
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     *
     * @return Token
     */
    public static function fromString($token)
    {
        return new self($token);
    }

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }
}
