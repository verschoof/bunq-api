<?php

namespace Bunq\Token\Storage;

use Bunq\Token\TokenType;

final class TokenNotFoundException extends \Exception
{
    /**
     * @param TokenType $type
     * @param string    $path
     */
    public function __construct(TokenType $type, $path)
    {
        parent::__construct(sprintf('Could not find token "%s" in path: %s', $type->toString(), $path), 0, null);
    }
}
