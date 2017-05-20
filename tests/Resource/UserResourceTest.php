<?php

namespace Bunq\Test\Resource;

use Bunq\BunqClient;
use Bunq\Resource\UserResource;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class UserResourceTest extends TestCase
{
    /**
     * @var BunqClient|ObjectProphecy
     */
    private $bunqClient;

    /**
     * @var UserResource
     */
    private $resource;

    public function setUp()
    {
        $this->bunqClient = $this->prophesize(BunqClient::class);
        $this->resource   = new UserResource($this->bunqClient->reveal());
    }

    /**
     * @test
     */
    public function itReturnsAListOfUsers()
    {
        $this->bunqClient->get('/v1/user')->willReturn(
            [
                'Response' => [
                    [
                        'UserCompany' => [
                            'id' => 1,
                        ],
                    ],
                ],
            ]
        );

        $result = $this->resource->listUsers();

        $expectedResult = [
            'Response' => [
                [
                    'UserCompany' => [
                        'id' => 1,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itReturnsASingleUser()
    {
        $this->bunqClient->get('/v1/user/1')->willReturn(
            [
                'Response' => [
                    [
                        'UserCompany' => [
                            'id' => 1,
                        ],
                    ],
                ],
            ]
        );

        $result = $this->resource->getUser(1);

        $expectedResult = [
            'id' => 1,
        ];

        $this->assertEquals($expectedResult, $result);
    }
}

