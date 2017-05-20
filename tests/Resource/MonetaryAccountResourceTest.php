<?php

namespace Bunq\Test\Resource;

use Bunq\BunqClient;
use Bunq\Resource\MonetaryAccountResource;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class MonetaryAccountResourceTest extends TestCase
{
    /**
     * @var BunqClient|ObjectProphecy
     */
    private $bunqClient;

    /**
     * @var MonetaryAccountResource
     */
    private $resource;

    public function setUp()
    {
        $this->bunqClient = $this->prophesize(BunqClient::class);
        $this->resource   = new MonetaryAccountResource($this->bunqClient->reveal());
    }

    /**
     * @test
     */
    public function ItGivesAListOfMonetaryAccounts()
    {
        $this->bunqClient->get('/v1/user/1/monetary-account')->willReturn(
            [
                'Response' => [
                    'MonetaryAccountBank' => [
                        [
                            'id' => 1,
                        ],
                        [
                            'id' => 2,
                        ],
                    ],
                ],
            ]
        );

        $result = $this->resource->listMonetaryAccounts(1);

        $expectedResult = [
            'Response' => [
                'MonetaryAccountBank' => [
                    [
                        'id' => 1,
                    ],
                    [
                        'id' => 2,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itGivesASingleMonetaryAccount()
    {
        $this->bunqClient->get('/v1/user/1/monetary-account/1')->willReturn(
            [
                'Response' => [
                    [
                        'MonetaryAccountBank' => [
                            'id' => 1,
                        ],
                    ],
                ],
            ]
        );

        $result = $this->resource->getMonetaryAccount(1, 1);

        $expectedResult = [
            'id' => 1,
        ];

        $this->assertEquals($expectedResult, $result);
    }
}

