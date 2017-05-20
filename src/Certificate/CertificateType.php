<?php

namespace Bunq\Certificate;

final class CertificateType
{
    const PRIVATE_KEY     = 'private.pem';
    const PUBLIC_KEY      = 'public.pem';
    const BUNQ_SERVER_KEY = 'public_server_key.pem';

    /**
     * @var string
     */
    private $certificateType;

    /**
     * @return self[]
     */
    public static function all()
    {
        return [
            self::PRIVATE_KEY(),
            self::PUBLIC_KEY(),
            self::BUNQ_SERVER_KEY(),
        ];
    }

    /**
     * @return string[]
     */
    public static function allAsString()
    {
        return [
            self::PRIVATE_KEY,
            self::PUBLIC_KEY,
            self::BUNQ_SERVER_KEY,
        ];
    }

    /**
     * @return CertificateType
     */
    public static function PRIVATE_KEY()
    {
        return new self(self::PRIVATE_KEY);
    }

    /**
     * @return CertificateType
     */
    public static function PUBLIC_KEY()
    {
        return new self(self::PUBLIC_KEY);
    }

    /**
     * @return CertificateType
     */
    public static function BUNQ_SERVER_KEY()
    {
        return new self(self::BUNQ_SERVER_KEY);
    }

    /**
     * @param $certificate
     *
     * @return CertificateType
     */
    public static function fromString($certificate)
    {
        return new self($certificate);
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
        return $this->certificateType;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param string $certificateType
     */
    private function __construct($certificateType)
    {
        $this->certificateType = (string)$certificateType;

        $this->protect();
    }

    /**
     * Check if the certificateType exists in our list
     */
    private function protect()
    {
        if (!in_array($this->certificateType, self::allAsString(), true)) {
            throw new \InvalidArgumentException(sprintf('Invalid certificate type %s', $this->certificateType));
        }
    }
}
