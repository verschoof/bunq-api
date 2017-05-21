<?php

namespace Bunq\Tests\Service;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\Service\DefaultInstallationService;
use Bunq\Token\DefaultToken;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;

final class InstallationServiceTest extends TestCase
{
    /**
     * @var ClientInterface|ObjectProphecy
     */
    private $httpClient;

    /**
     * @var CertificateStorage|ObjectProphecy
     */
    private $certificateStorage;

    /**
     * @var DefaultInstallationService
     */
    private $service;

    public function setUp()
    {
        $this->httpClient         = $this->prophesize(ClientInterface::class);
        $this->certificateStorage = $this->prophesize(CertificateStorage::class);

        $this->service = new DefaultInstallationService(
            $this->httpClient->reveal(),
            $this->certificateStorage->reveal(),
            'apiKey',
            [
                'ip1',
                'ip2',
            ]
        );
    }

    /**
     * @test
     */
    public function itCallsTheInstallationCallWithPublicKey()
    {
        $publicKey = DefaultCertificate::fromString('-----BEGIN PUBLIC KEY-----KEY-----END PUBLIC KEY-----');

        $this->certificateStorage->load(CertificateType::PUBLIC_KEY())->willReturn($publicKey);

        $postOptions = [
            'json' => [
                'client_public_key' => $publicKey->toString(),
            ],
        ];

        $expectedToken = 'installation-token';
        $expectedKey   = '-----BEGIN PUBLIC KEY-----NEW-KEY-----END PUBLIC KEY-----';

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn(
            \json_encode(
                [
                    'Response' => [
                        [
                            'Id' => [
                                'id' => 1,
                            ],
                        ],
                        [
                            'Token' => [
                                'token' => $expectedToken,
                            ],
                        ],
                        [
                            'ServerPublicKey' => [
                                'server_public_key' => $expectedKey,
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->httpClient->request('POST', '/v1/installation', $postOptions)->willReturn(
            $response->reveal()
        );

        $result = $this->service->install();

        $this->assertEquals(
            [
                'token'      => $expectedToken,
                'public_key' => $expectedKey,
            ],
            $result
        );
    }

    /**
     * @test
     */
    public function itRegistersADevice()
    {
        $installationToken = new DefaultToken('installation-token');

        $postOptions = [
            'headers' => [
                'X-Bunq-Client-Authentication' => 'installation-token',
            ],
            'json' => [
                'description'   => 'Bunq PHP API Client',
                'secret'        => 'apiKey',
                'permitted_ips' => ['ip1', 'ip2'],
            ],
        ];

        $this->httpClient->request('POST', '/v1/device-server', $postOptions)->shouldBeCalled();

        $this->service->registerDevice($installationToken);
    }

    /**
     * @test
     */
    public function itCreatesASession()
    {
        $installationToken = new DefaultToken('installation-token');

        $postOptions = [
            'headers' => [
                'X-Bunq-Client-Authentication' => 'installation-token',
            ],
            'json'    => [
                'secret' => 'apiKey',
            ]
        ];

        $expectedToken = 'session-token';

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn(
            \json_encode(
                [
                    'Response' => [
                        [
                            'Id' => [
                                'id' => 1,
                            ],
                        ],
                        [
                            'Token' => [
                                'token' => $expectedToken,
                            ],
                        ]
                    ],
                ]
            )
        );

        $this->httpClient->request('POST', '/v1/session-server', $postOptions)->willReturn(
            $response->reveal()
        );

        $result = $this->service->createSession($installationToken);

        $this->assertEquals($expectedToken, $result);
    }
}

