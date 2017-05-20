<?php

namespace Bunq\Resource;

use Bunq\BunqClient;

final class UserResource
{
    /**
     * @var BunqClient
     */
    private $client;

    /**
     * @param BunqClient $client
     */
    public function __construct(BunqClient $client)
    {
        $this->client = $client;
    }

    /**
     * Lists all users within the current session.
     *
     * @return array
     */
    public function listUsers()
    {
        return $this->client->get($this->getResourceEndpoint());
    }

    /**
     * Gets a user its information.
     *
     * @param integer $id
     *
     * @return array
     */
    public function getUser($id)
    {
        $response = $this->client->get($this->getResourceEndpoint() . '/' . (int)$id);

        return $response['Response'][0]['UserCompany'];
    }

    /**
     * @return string
     */
    private function getResourceEndpoint()
    {
        return '/v1/user';
    }
}
