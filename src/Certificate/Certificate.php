<?php

namespace Bunq\Certificate;

interface Certificate
{
    /**
     * @param string $certificate
     *
     * @return self
     */
    public static function fromString($certificate);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function toString();
}
