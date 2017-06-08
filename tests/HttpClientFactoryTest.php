<?php

namespace Bunq\Tests;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\HttpClientFactory;
use Bunq\Middleware\RefreshSessionMiddleware;
use Bunq\Middleware\RequestAuthenticationMiddleware;
use Bunq\Middleware\RequestIdMiddleware;
use Bunq\Middleware\RequestSignatureMiddleware;
use Bunq\Middleware\ResponseSignatureMiddleware;
use Bunq\Service\InstallationService;
use Bunq\Service\TokenService;
use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\TokenStorage;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class HttpClientFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesAInstallationClient()
    {
        $baseUrl            = 'uri';
        $privateCertificate = DefaultCertificate::fromString('private-certificate');

        /** @var CertificateStorage|ObjectProphecy $certificateStorage */
        $certificateStorage = $this->prophesize(CertificateStorage::class);
        $certificateStorage->load(CertificateType::PRIVATE_KEY())->willReturn($privateCertificate);

        $client = HttpClientFactory::createInstallationClient($baseUrl, $certificateStorage->reveal());

        $expectedHandlerStack = HandlerStack::create();
        $expectedHandlerStack->push(
            Middleware::mapRequest(new RequestIdMiddleware())
        );
        $expectedHandlerStack->push(
            Middleware::mapRequest(new RequestSignatureMiddleware($privateCertificate))
        );

        $expectedClient = new \GuzzleHttp\Client(
            [
                'base_uri' => $baseUrl,
                'handler'  => $expectedHandlerStack,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'User-Agent'   => 'bunq-api-client:user',
                ],
            ]
        );

        $this->assertEquals($expectedClient, $client);
    }

    /**
     * @test
     */
    public function itCreatesADefaultClient()
    {
        $baseUrl = 'uri';

        $privateCertificate = DefaultCertificate::fromString('private-certificate');
        $bunqCertificate    = DefaultCertificate::fromString('bunq-certificate');
        /** @var CertificateStorage|ObjectProphecy $certificateStorage */
        $certificateStorage = $this->prophesize(CertificateStorage::class);
        $certificateStorage->load(CertificateType::PRIVATE_KEY())->willReturn($privateCertificate);
        $certificateStorage->load(CertificateType::BUNQ_SERVER_KEY())->willReturn($bunqCertificate);

        $sessionToken = DefaultToken::fromString('session-token');
        /** @var TokenService|ObjectProphecy $tokenService */
        $tokenService = $this->prophesize(TokenService::class);
        $tokenService->sessionToken()->willReturn($sessionToken);

        /** @var InstallationService|ObjectProphecy $installationService */
        $installationService = $this->prophesize(InstallationService::class);

        /** @var TokenStorage|ObjectProphecy $tokenStorage */
        $tokenStorage = $this->prophesize(TokenStorage::class);

        $client = HttpClientFactory::create(
            $baseUrl,
            $tokenService->reveal(),
            $certificateStorage->reveal(),
            $installationService->reveal(),
            $tokenStorage->reveal()
        );

        $expectedHandlerStack = HandlerStack::create();
        $expectedHandlerStack->push(
            Middleware::mapRequest(new RequestIdMiddleware())
        );
        $expectedHandlerStack->push(
            Middleware::mapRequest(new RequestAuthenticationMiddleware($sessionToken))
        );
        $expectedHandlerStack->push(
            Middleware::mapRequest(new RequestSignatureMiddleware($bunqCertificate))
        );
        $expectedHandlerStack->push(
            Middleware::mapResponse(new ResponseSignatureMiddleware($bunqCertificate))
        );
        $expectedHandlerStack->push(
            Middleware::mapResponse(new RefreshSessionMiddleware($installationService->reveal(), $tokenStorage->reveal()))
        );

        $expectedClient = new \GuzzleHttp\Client(
            [
                'base_uri' => $baseUrl,
                'handler'  => $expectedHandlerStack,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'User-Agent'   => 'bunq-api-client:user',
                ],
            ]
        );

        $this->assertEquals($expectedClient, $client);
    }
}

