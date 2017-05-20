<?php

namespace Bunq\Certificate\Storage;

use Bunq\Certificate\CertificateType;

final class CertificateNotFoundException extends \Exception
{
    /**
     * @param CertificateType $certificateType
     * @param string          $path
     */
    public function __construct(CertificateType $certificateType, $path)
    {
        parent::__construct(sprintf('Could not find certificate "%s" in: %s', $certificateType->toString(), $path), 0, null);
    }
}
