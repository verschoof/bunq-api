<?php

namespace Bunq\Tests\Certificate\Storage;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;
use Bunq\Certificate\Storage\FileCertificateStorage;
use PHPUnit\Framework\TestCase;

final class FileCertificateStorageTest extends TestCase
{
    /**
     * @var FileCertificateStorage
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new FileCertificateStorage('/tmp');
    }

    /**
     * @test
     */
    public function itFindsACertificatedBasedOnAType()
    {
        $certificate = DefaultCertificate::fromString('savedCertificate');
        $publicKey   = CertificateType::PRIVATE_KEY();

        @mkdir('/tmp/certificates');
        file_put_contents('/tmp/certificates/' . $publicKey, $certificate->toString());

        $loadedCertificate = $this->storage->load($publicKey);

        $this->assertSame($certificate->toString(), $loadedCertificate->toString());
    }

    /**
     * @test
     */
    public function itSavesACertificatesBasedOnAType()
    {
        $certificate = DefaultCertificate::fromString('test');
        $publicKey   = CertificateType::PUBLIC_KEY();

        $this->storage->save($certificate, $publicKey);

        $result = @file_get_contents('/tmp/certificates/' . $publicKey);

        $this->assertSame($certificate->toString(), $result);
    }
}
