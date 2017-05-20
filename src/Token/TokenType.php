<?php

namespace Bunq\Token;

final class TokenType
{
    const INSTALLATION_TOKEN = 'installation.token';
    const SESSION_TOKEN      = 'session.token';

    /**
     * @var string
     */
    private $type;

    /**
     * @return self[]
     */
    public static function all()
    {
        return [
            self::INSTALLATION_TOKEN(),
            self::SESSION_TOKEN(),
        ];
    }

    /**
     * @return string[]
     */
    public static function allAsString()
    {
        return [
            self::INSTALLATION_TOKEN,
            self::SESSION_TOKEN,
        ];
    }

    /**
     * @return self
     */
    public static function INSTALLATION_TOKEN()
    {
        return new self(self::INSTALLATION_TOKEN);
    }

    /**
     * @return self
     */
    public static function SESSION_TOKEN()
    {
        return new self(self::SESSION_TOKEN);
    }

    /**
     * @param $type
     *
     * @return self
     */
    public static function fromString($type)
    {
        return new self($type);
    }

    /**
     * @param mixed $other
     *
     * @return bool
     */
    public function equals($other)
    {
        return $other == $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param string $type
     */
    private function __construct($type)
    {
        $this->type = (string)$type;

        $this->protect();
    }

    /**
     * Check if the tokenType exists in our list
     */
    private function protect()
    {
        if (!in_array($this->type, self::allAsString(), true)) {
            throw new \InvalidArgumentException(sprintf('Invalid token type "%s"', $this->type));
        }
    }
}
