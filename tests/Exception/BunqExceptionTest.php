<?php

namespace Bunq\Tests\Exception;

use Bunq\Exception\BunqException;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class BunqExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsABunqException()
    {
        /** @var UriInterface|ObjectProphecy $uri */
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn('/path');

        /** @var RequestInterface|ObjectProphecy $request */
        $request  = $this->prophesize(RequestInterface::class);
        $request->getUri()->willReturn($uri);

        /** @var ResponseInterface|ObjectProphecy $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn('body');
        $response->getStatusCode()->willReturn(403);

        $clientException = new ClientException('Message', $request->reveal(), $response->reveal());

        $bunqException = new BunqException($clientException);

        $this->assertInstanceOf(BunqException::class, $bunqException);
        $this->assertInstanceOf(\Exception::class, $bunqException);
        $this->assertSame('Path: /path, Message: body', $bunqException->getMessage());
        $this->assertSame(403, $bunqException->getCode());
    }
}
