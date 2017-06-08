<?php

namespace Bunq\Tests\Middleware;

use Bunq\Middleware\RefreshSessionMiddleware;
use Bunq\Service\InstallationService;
use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\TokenStorage;
use Bunq\Token\TokenType;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;

final class RefreshSessionMiddlewareTest extends TestCase
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
     * @var RefreshSessionMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $this->installationService = $this->prophesize(InstallationService::class);
        $this->tokenStorage        = $this->prophesize(TokenStorage::class);

        $this->middleware = new RefreshSessionMiddleware(
            $this->installationService->reveal(),
            $this->tokenStorage->reveal()
        );
    }

    /**
     * @test
     */
    public function itWontGetExecutedWhenResponseIsNot401()
    {
        $response = new Response(200, [], '{"ok"}');

        $this->tokenStorage->load(Argument::any())->shouldNotBeCalled();

        $resultResponse = $this->middleware->__invoke($response);

        $this->assertSame($response, $resultResponse);
    }

    /**
     * @test
     */
    public function itWontGetExecutedWhenResponseIsNotInsufficientAuthorisation()
    {
        $body = '{"Error":[{"error_description": "An other error"}]}';
        $response = new Response(401, [], $body);

        $this->tokenStorage->load(Argument::any())->shouldNotBeCalled();

        $resultResponse = $this->middleware->__invoke($response);

        $this->assertEquals($response, $resultResponse);
    }

    /**
     * @test
     * @expectedException \Bunq\Exception\SessionWasExpiredException
     */
    public function itWillRefreshSessionAndThrowsAnExceptionIfInsufficientAuthorisation()
    {
        $body = '{"Error":[{"error_description": "Insufficient authorisation."}]}';
        $response = new Response(401, [], $body);

        $installationToken = new DefaultToken('Installation Token');
        $this->tokenStorage->load(TokenType::INSTALLATION_TOKEN())->willReturn($installationToken);
        $this->installationService->createSession($installationToken)->willReturn('someSessionId');

        $sessionToken = DefaultToken::fromString('someSessionId');

        $this->tokenStorage->save($sessionToken, TokenType::SESSION_TOKEN())->shouldBeCalled();

        $this->middleware->__invoke($response);
    }
}

