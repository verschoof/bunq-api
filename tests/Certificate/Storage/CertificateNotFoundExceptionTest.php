<?php

namespace Bunq\Test\Certificate\Storage;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\Storage\CertificateNotFoundException;
use PHPUnit\Framework\TestCase;

final class CertificateNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsACertificateNotFoundException()
    {
        $exception = new CertificateNotFoundException(CertificateType::PRIVATE_KEY(), 'path');

        $this->assertEquals('Could not find certificate "private.pem" in: path', $exception->getMessage());
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
