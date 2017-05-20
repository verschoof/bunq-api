<?php

namespace Bunq\Tests\Resource;

use Bunq\BunqClient;
use Bunq\Resource\PaymentResource;
use PHPUnit\Framework\TestCase;

final class PaymentResourceTest extends TestCase
{
    /**
     * @var BunqClient
     */
    private $bunqClient;

    /**
     * @var PaymentResource
     */
    private $resource;

    public function setUp()
    {
        $this->bunqClient = $this->prophesize(BunqClient::class);
        $this->resource   = new PaymentResource($this->bunqClient->reveal());
    }

    /**
     * @test
     */
    public function itReturnsAListOfPayments()
    {
        $this->bunqClient->get('/v1/user/1/monetary-account/1/payment')->willReturn(
            [
                'Response' => [
                    [
                        'Payment' => [
                            'amount' => [
                                'value'    => '12.50',
                                'currency' => 'EUR',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $result = $this->resource->listPayments(1, 1);

        $expectedResult = [
            'Response' => [
                [
                    'Payment' => [
                        'amount' => [
                            'value'    => 1250,
                            'currency' => 'EUR',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itReturnsASinglePayment()
    {
        $this->bunqClient->get('/v1/user/1/monetary-account/1/payment/1')->willReturn(
            [
                'Response' => [
                    [
                        'Payment' => [
                            'amount' => [
                                'value'    => '12.50',
                                'currency' => 'EUR',
                            ],
                        ],

                    ],
                ],
            ]
        );

        $result = $this->resource->getPayment(1, 1, 1);

        $expectedResult = [
            'amount' => [
                'value'    => '1250',
                'currency' => 'EUR',
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
