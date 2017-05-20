<?php

namespace Bunq\Test\Certificate;

use Bunq\Certificate\CertificateType;
use PHPUnit\Framework\TestCase;

final class CertificateTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsACertificateType()
    {
        $certificateType = CertificateType::fromString('private.pem');

        $this->assertInstanceOf(CertificateType::class, $certificateType);
        $this->assertEquals($certificateType->toString(), 'private.pem');
    }

    /**
     * @test
     */
    public function itGivesAllTypesAsList()
    {
        $expected = [
            CertificateType::PRIVATE_KEY(),
            CertificateType::PUBLIC_KEY(),
            CertificateType::BUNQ_SERVER_KEY(),
        ];
        $result = CertificateType::all();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itGivesAllTypesAsStringList()
    {
        $expected = [
            'private.pem',
            'public.pem',
            'public_server_key.pem',
        ];
        $result = CertificateType::allAsString();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function itTellsIfItsEqualOrNot()
    {
        $certificate = CertificateType::PRIVATE_KEY();

        $this->assertTrue($certificate->equals(CertificateType::PRIVATE_KEY()));
        $this->assertFalse($certificate->equals(CertificateType::PUBLIC_KEY()));
        $this->assertFalse($certificate->equals(new \stdClass()));
        $this->assertFalse($certificate->equals('private key'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid certificate type test
     */
    public function itThrowsAnErrorWhenTypeIsNotKnown()
    {
        CertificateType::fromString('test');
    }
}
