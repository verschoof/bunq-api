<?php

namespace Bunq\Service;

use Bunq\Token\Token;

interface TokenService
{
    /**
     * @return Token
     */
    public function sessionToken();
}
