<?php

namespace Bunq\Test\Service;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\Service\InstallationService;
use Bunq\Service\DefaultTokenService;
use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\TokenNotFoundException;
use Bunq\Token\Storage\TokenStorage;
use Bunq\Token\TokenType;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

final class TokenServiceTest extends TestCase
{
    /**
     * @var InstallationService|ObjectProphecy
     */
    private $installationService;

    /**
     * @var TokenStorage|ObjectProphecy
     */
    private $tokenStorage;

    /**
     * @var CertificateStorage|ObjectProphecy
     */
    private $certificateStorage;

    /**
     * @var DefaultTokenService
     */
    private $service;

    public function setUp()
    {
        $this->installationService = $this->prophesize(InstallationService::class);
        $this->tokenStorage        = $this->prophesize(TokenStorage::class);
        $this->certificateStorage  = $this->prophesize(CertificateStorage::class);

        $this->service = new DefaultTokenService(
            $this->installationService->reveal(),
            $this->tokenStorage->reveal(),
            $this->certificateStorage->reveal()
        );
    }

    /**
     * @test
     */
    public function itReturnsASessionToken()
    {
        $token = new DefaultToken('someToken');
        $this->tokenStorage->load(TokenType::SESSION_TOKEN())->willReturn($token);
        $this->installationService->createSession(Argument::any())->shouldNotBeCalled();

        $result = $this->service->sessionToken();

        $this->assertEquals($token, $result);
    }

    /**
     * @test
     */
    public function itCreatesANewSessionTokenWhenItDoesNotExist()
    {
        $this->tokenStorage->load(TokenType::SESSION_TOKEN())->willThrow(
            new TokenNotFoundException(TokenType::SESSION_TOKEN(), 'path')
        );

        $installationToken = new DefaultToken('installation_token');
        $this->tokenStorage->load(TokenType::INSTALLATION_TOKEN())->willReturn($installationToken);

        $this->installationService->install()->shouldNotBeCalled();
        $this->installationService->createSession($installationToken)->willReturn('a-long-new-session-token');
        $this->installationService->registerDevice(Argument::any())->shouldNotBeCalled();

        $sessionToken = DefaultToken::fromString('a-long-new-session-token');

        $this->tokenStorage->save($sessionToken, TokenType::SESSION_TOKEN())->shouldBeCalled();

        $this->assertEquals($sessionToken, $this->service->sessionToken());
    }

    /**
     * @test
     */
    public function itCreatesANewSessionTokenAndInstallationTokenWithThePublicKey()
    {
        $this->tokenStorage->load(TokenType::SESSION_TOKEN())->willThrow(
            new TokenNotFoundException(TokenType::SESSION_TOKEN(), 'path')
        );
        $this->tokenStorage->load(TokenType::INSTALLATION_TOKEN())->willThrow(
            new TokenNotFoundException(TokenType::INSTALLATION_TOKEN(), 'path')
        );

        $this->installationService->install()->willReturn(
            [
                'token'      => 'a-long-new-installation-token',
                'public_key' => '-----BEGIN PUBLIC KEY-----KEY-----END PUBLIC KEY-----'
            ]
        );

        $installationToken = DefaultToken::fromString('a-long-new-installation-token');
        $this->tokenStorage->save($installationToken, TokenType::INSTALLATION_TOKEN())->shouldBeCalled();

        $certificate = DefaultCertificate::fromString('-----BEGIN PUBLIC KEY-----KEY-----END PUBLIC KEY-----');
        $this->certificateStorage->save($certificate, CertificateType::BUNQ_SERVER_KEY())->shouldBeCalled();

        $this->installationService->registerDevice($installationToken)->shouldBeCalled();

        $this->installationService->createSession($installationToken)->willReturn('a-long-new-session-token');

        $sessionToken = DefaultToken::fromString('a-long-new-session-token');

        $this->tokenStorage->save($sessionToken, TokenType::SESSION_TOKEN())->shouldBeCalled();

        $this->assertEquals($sessionToken, $this->service->sessionToken());
    }
}

