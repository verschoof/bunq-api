<?php

namespace Bunq\Test\Resource;

use Bunq\BunqClient;
use Bunq\Resource\CardResource;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class CardResourceTest extends TestCase
{
    /**
     * @var BunqClient|ObjectProphecy
     */
    private $bunqClient;

    /**
     * @var CardResource
     */
    private $resource;

    public function setUp()
    {
        $this->bunqClient = $this->prophesize(BunqClient::class);
        $this->resource   = new CardResource($this->bunqClient->reveal());
    }

    /**
     * @test
     */
    public function itReturnsAListOfCards()
    {
        $this->bunqClient->get('/v1/user/1/card')->willReturn(
            [
                'Response' => [
                    [
                        'id' => 1,
                    ],
                    [
                        'id' => 2,
                    ],
                ],
            ]
        );

        $result = $this->resource->listCards(1);

        $expectedResult = [
            'Response' => [
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itReturnsASingleCard()
    {
        $this->bunqClient->get('/v1/user/1/card/1')->willReturn(
            [
                'Response' => [
                    [
                        'id' => 1,
                    ],
                ],
            ]
        );

        $result = $this->resource->getCard(1, 1);

        $expectedResult = [
            'id' => 1,
        ];

        $this->assertEquals($expectedResult, $result);
    }
}

