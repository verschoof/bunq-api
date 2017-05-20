<?php

namespace Bunq\Certificate\Storage;

use Bunq\Certificate\Certificate;
use Bunq\Certificate\CertificateType;

interface CertificateStorage
{
    /**
     * @param CertificateType $certificateType
     *
     * @return Certificate
     */
    public function load(CertificateType $certificateType);

    /**
     * @param Certificate     $certificate
     * @param CertificateType $certificateType
     *
     * @return void
     */
    public function save(Certificate $certificate, CertificateType $certificateType);
}
