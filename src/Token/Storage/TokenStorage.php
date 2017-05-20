<?php

namespace Bunq\Token\Storage;

use Bunq\Token\Token;
use Bunq\Token\TokenType;

interface TokenStorage
{
    /**
     * @param TokenType $type
     *
     * @return Token
     */
    public function load(TokenType $type);

    /**
     * @param Token     $token
     * @param TokenType $type
     *
     * @return void
     */
    public function save(Token $token, TokenType $type);
}
