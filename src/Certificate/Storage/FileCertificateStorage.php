<?php

namespace Bunq\Certificate\Storage;

use Bunq\Certificate\Certificate;
use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;

final class FileCertificateStorage implements CertificateStorage
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
        $this->path = (string)$path . '/certificates';
    }

    /**
     * {@inheritdoc}
     */
    public function load(CertificateType $certificateType)
    {
        if (!file_exists($this->path . '/' . $certificateType->toString())) {
            throw new CertificateNotFoundException($certificateType, $this->path);
        }

        $certificate = $this->loadCertificate($certificateType);

        return DefaultCertificate::fromString($certificate);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Certificate $certificate, CertificateType $certificateType)
    {
        $certificate = trim($certificate->toString());

        if (!file_exists($this->path)) {
            mkdir($this->path);
        }

        file_put_contents($this->path . '/' . $certificateType, $certificate);

        // save server
        $this->cache[$certificateType->toString()] = $certificate;
    }

    /**
     * @param CertificateType $certificateType
     *
     * @return string
     *
     * @throws CertificateNotFoundException
     */
    private function loadCertificate(CertificateType $certificateType)
    {
        // take from cache, filesystems are slow
        if (isset($this->cache[$certificateType->toString()])) {
            return $this->cache[$certificateType->toString()];
        }

        // load from file system
        $certificate = file_get_contents($this->path . '/' . $certificateType->toString());

        if (!$certificate) {
            throw new CertificateNotFoundException($certificateType, $this->path);
        }

        // save the result in the cache
        $this->cache[$certificateType->toString()] = $certificate;

        return $certificate;
    }
}
