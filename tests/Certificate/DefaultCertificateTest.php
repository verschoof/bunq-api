<?php

namespace Bunq\Tests\Certificate;

use Bunq\Certificate\Certificate;
use Bunq\Certificate\DefaultCertificate;
use PHPUnit\Framework\TestCase;

final class DefaultCertificateTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsADefaultCertificate()
    {
       $certificate = DefaultCertificate::fromString('certificate');

       $this->assertInstanceOf(DefaultCertificate::class, $certificate);
       $this->assertInstanceOf(Certificate::class, $certificate);
       $this->assertEquals('certificate', $certificate->toString());
    }
}
