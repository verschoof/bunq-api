<?php

namespace Bunq\Certificate;

final class DefaultCertificate implements Certificate
{
    /**
     * @var string
     */
    private $certificate;

    /**
     * @param string $certificate
     *
     * @return DefaultCertificate
     */
    public static function fromString($certificate)
    {
        return new self($certificate);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->certificate;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * @param string $certificate
     */
    private function __construct($certificate)
    {
        $this->certificate = (string)$certificate;
    }
}
