<?php

namespace Bunq\Test\Middleware;

use Bunq\Middleware\RequestIdMiddleware;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

final class RequestIdMiddlewareTest extends TestCase
{
    /**
     * @var RequestIdMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $this->middleware = new RequestIdMiddleware();
    }

    /**
     * @test
     */
    public function itSetsAllHeadersAndRequestIdWhenNotGiven()
    {
        $request = new Request('GET', 'uri', ['other-existing-header' => 'value']);

        // Create a Uuid object from a known UUID string
        $stringUuid = '253e0f90-8842-4731-91dd-0191816e6a28';
        $uuid       = Uuid::fromString($stringUuid);

        /** @var UuidFactory|ObjectProphecy $factoryMock */
        $factoryMock = $this->prophesize(UuidFactory::class);
        $factoryMock->uuid4()->willReturn($uuid);

        // Replace the default factory with our mock
        Uuid::setFactory($factoryMock->reveal());

        $result = $this->middleware->__invoke($request);

        $headers         = [
            'X-Bunq-Client-Request-Id' => '253e0f90-8842-4731-91dd-0191816e6a28',
            'Cache-Control'            => 'no-cache',
            'X-Bunq-Geolocation'       => '52.3 4.89 12 100 NL',
            'X-Bunq-Language'          => 'nl_NL',
            'X-Bunq-Region'            => 'nl_NL',
            'other-existing-header'    => 'value',
        ];
        $expectedRequest = new Request('GET', 'uri', $headers, $request->getBody());

        $this->assertEquals($expectedRequest, $result);
    }

    /**
     * @test
     */
    public function itSetsAllHeadersAndNotRequestIdWhenGiven()
    {
        $givenRequestId = '253e0f90-8842-4731-91dd-0191816e6a29';
        $request        = new Request('GET', 'uri', []);

        $result = $this->middleware->__invoke($request, ['request-id' => $givenRequestId]);

        $headers         = [
            'X-Bunq-Client-Request-Id' => $givenRequestId,
            'Cache-Control'            => 'no-cache',
            'X-Bunq-Geolocation'       => '52.3 4.89 12 100 NL',
            'X-Bunq-Language'          => 'nl_NL',
            'X-Bunq-Region'            => 'nl_NL',
        ];
        $expectedRequest = new Request('GET', 'uri', $headers, $request->getBody());

        $this->assertEquals($expectedRequest, $result);

    }
}

