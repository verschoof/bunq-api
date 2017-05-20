<?php

namespace Bunq\Test\Middleware;

use Bunq\Middleware\RequestAuthenticationMiddleware;
use Bunq\Token\DefaultToken;
use Bunq\Token\Token;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

final class RequestAuthenticationMiddlewareTest extends TestCase
{
    /**
     * @var Token
     */
    private $sessionToken;

    /**
     * @var RequestAuthenticationMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $this->sessionToken = DefaultToken::fromString('session-token');
        $this->middleware   = new RequestAuthenticationMiddleware($this->sessionToken);
    }

    /**
     * @test
     */
    public function itAddsAHeaderWhenAuthenticationHeaderIsNotSet()
    {
        $request = new Request('GET', 'uri', []);

        $request = $this->middleware->__invoke($request);

        $headers         = [
            'X-Bunq-Client-Authentication' => 'session-token',
        ];
        $expectedRequest = new Request('GET', 'uri', $headers, $request->getBody());

        $this->assertEquals($expectedRequest, $request);
    }

    /**
     * @test
     */
    public function itDoesNotAddAHeaderWhenAuthenticationHeaderIsAlreadySet()
    {
        $request = new Request(
            'GET',
                'uri',
                [
                    'X-Bunq-Client-Authentication' => 'own-session-token',
                ]
        );

        $request = $this->middleware->__invoke($request);

        $headers         = [
            'X-Bunq-Client-Authentication' => 'own-session-token',
        ];
        $expectedRequest = new Request('GET', 'uri', $headers, $request->getBody());

        $this->assertEquals($expectedRequest, $request);
    }
}

