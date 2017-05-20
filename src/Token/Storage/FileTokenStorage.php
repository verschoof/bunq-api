<?php

namespace Bunq\Token\Storage;

use Bunq\Token\DefaultToken;
use Bunq\Token\Token;
use Bunq\Token\TokenType;

final class FileTokenStorage implements TokenStorage
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = (string)$path . '/tokens';
    }

    /**
     * @inheritdoc
     */
    public function load(TokenType $type)
    {
        if (!file_exists($this->path . '/' . $type->toString())) {
            throw new TokenNotFoundException($type, $this->path);
        }

        $token = $this->loadToken($type);

        return DefaultToken::fromString($token);
    }

    /**
     * @inheritdoc
     */
    public function save(Token $token, TokenType $type)
    {
        $token = trim($token->toString());

        if (!file_exists($this->path)) {
            mkdir($this->path);
        }

        file_put_contents($this->path . '/' . $type->toString(), $token);

        $this->cache[$type->toString()] = $token;
    }

    /**
     * @param TokenType $type
     *
     * @return string
     *
     * @throws TokenNotFoundException
     */
    private function loadToken(TokenType $type)
    {
        // take from cache, filesystems are slow
        if (isset($this->cache[$type->toString()])) {
            return $this->cache[$type->toString()];
        }

        $token = trim(file_get_contents($this->path . '/' . $type->toString()));

        if (!$token) {
            throw new TokenNotFoundException($type, $this->path);
        }

        // save the result in the cache
        $this->cache[$type->toString()] = $token;

        return $token;
    }
}
