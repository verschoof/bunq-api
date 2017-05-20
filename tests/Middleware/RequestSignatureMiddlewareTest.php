<?php

namespace Bunq\Tests\Middleware;

use Bunq\Certificate\DefaultCertificate;
use Bunq\Middleware\RequestSignatureMiddleware;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

final class RequestSignatureMiddlewareTest extends TestCase
{
    /**
     * @var RequestSignatureMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $privKey = '-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAMFTIP07VOb/jeax
cScI/Mnp9wv0Q03kTCCPcaPoOI0nXM8jMJyu4JuLcLX8OnITJhsrNj2ff3lwDsc6
KgejUbcGMJp6xPgALXdkztOPEWNYxCIyb9Pj8KctwUlu679pD6WoD60JMh88lL2o
A8VWJkLShBHaA0yqEWHfiPBGfnCfAgMBAAECgYAZzuwvgjUyZVlfO778VBHTLr3B
RcmaY2jaS+wC0qSCC4gzSuJZeGsZuMLCOLrgsbe7lsg1784HuzLt29DylhX5WpN3
7oJ7j6/GxElrgx4945N7syqMgrSfu+B8MiemFJqWrMNv5WXSIG/bA+O/nS1bGhLb
MkmCtGYca2XL674v0QJBAOdppnzV11n2pG8D7J2dwPRCOFhHQnfU6vkamY+RUETN
+jEIRO3GLp59S52Lb/W1FWdr+CIurynJnSvHary+1ncCQQDV3X+BrpOq09KT8Kdu
oqBhA7vz5oHgph/aK9lu0mytJZJR5OOmVFcygFTCNBzRgWiog1yLeu6dIvbH/3Tu
fTkZAkA0y73YLVF8jPDnAxYwv5UOJS2FtnxLqqARRShTwOt3RycVmLVhKh3out1N
jEp23GGNvuxVoCEGf6EEI/MOLNvxAkBMnybnl1xgf7OaPUY/ZRSArY3RPGybx+jx
iXRFOC6neiaKIK9PEDiNZ1z2lyUcCnkspqOpdUm4EqbH0MZcC5FBAkEA5Y1FKMSq
VfRE5LPsqFeT4lPFUX8EP20ucM86v1KtG4ywZxLOLOZ80e0V9SxZN17VDBwh5tVd
BxZd7SRnxYD9TQ==
-----END PRIVATE KEY-----';

        $certificate      = DefaultCertificate::fromString($privKey);
        $this->middleware = new RequestSignatureMiddleware($certificate);
    }

    /**
     * @test
     */
    public function itSignsARequest()
    {
        $headers         = [
            'X-Bunq-Client-Request-Id'     => '253e0f90-8842-4731-91dd-0191816e6a28',
            'X-Bunq-Client-Authentication' => 'session-token',
            'Cache-Control'                => 'no-cache',
            'X-Bunq-Geolocation'           => '52.3 4.89 12 100 NL',
            'X-Bunq-Language'              => 'nl_NL',
            'X-Bunq-Region'                => 'nl_NL',
            'other-existing-header'        => 'value',
        ];
        $request = new Request('GET', 'uri', $headers);

        $result = $this->middleware->__invoke($request);

        $expectedHeaders         = [
            'X-Bunq-Client-Request-Id'     => '253e0f90-8842-4731-91dd-0191816e6a28',
            'X-Bunq-Client-Authentication' => 'session-token',
            'Cache-Control'                => 'no-cache',
            'X-Bunq-Geolocation'           => '52.3 4.89 12 100 NL',
            'X-Bunq-Language'              => 'nl_NL',
            'X-Bunq-Region'                => 'nl_NL',
            'other-existing-header'        => 'value',
            'X-Bunq-Client-Signature'      => 'UdNaWrEGs9KqY33r3YSf4KpayASnT6WIgIRt98TWmhZ4qcG+a1N641K39ao9lZkNRuBYC2wH4FHthCWeE7qZlUMDazLh2fgC3+YkqMz5wZe26dRbQUWOK+8uR3OodaGAVfGxt4elwPy50nMjMvCgbH0DQuo8lMCrHwkhamBznJw='
        ];
        $expectedRequest = new Request('GET', 'uri', $expectedHeaders, $request->getBody());

        $this->assertEquals($expectedRequest, $result);
    }
}

