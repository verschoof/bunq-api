<?php

namespace Bunq\Token;

interface Token
{
    /**
     * @param string $token
     *
     * @return \Bunq\Token\Token
     */
    public static function fromString($token);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function toString();
}
