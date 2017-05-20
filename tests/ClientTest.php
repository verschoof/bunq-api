<?php

namespace Bunq\Test;

use Bunq\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class ClientTest extends TestCase
{
    /**
     * @var ClientInterface|ObjectProphecy
     */
    private $httpClient;

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->httpClient = $this->prophesize(ClientInterface::class);
        $this->client = new Client($this->httpClient->reveal());
    }

    /**
     * @test
     */
    public function itExecutesAGetRequest()
    {
        /** @var Response|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->willReturn(
            json_encode([
                'Response' => [
                    'status' => 'OK'
                ]
            ])
        );

        $this->httpClient->request('GET', 'uri', [])->willReturn($response->reveal());

        $result = $this->client->get('uri');

        $expectedResult = ['Response' => ['status' => 'OK']];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itExecutesAPostRequest()
    {
        /** @var Response|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->willReturn(
            json_encode([
                'Response' => [
                    'status' => 'OK'
                ]
            ])
        );

        $this->httpClient->request('POST', 'uri', [])->willReturn($response->reveal());

        $result = $this->client->post('uri');

        $expectedResult = ['Response' => ['status' => 'OK']];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itExecutesAPutRequest()
    {
        /** @var Response|ObjectProphecy $response */
        $response = $this->prophesize(Response::class);
        $response->getBody()->willReturn(
            json_encode([
                'Response' => [
                    'status' => 'OK'
                ]
            ])
        );

        $this->httpClient->request('PUT', 'uri', [])->willReturn($response->reveal());

        $result = $this->client->put('uri');

        $expectedResult = ['Response' => ['status' => 'OK']];

        $this->assertEquals($expectedResult, $result);
    }
}

