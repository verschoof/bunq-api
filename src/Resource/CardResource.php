<?php

namespace Bunq\Resource;

use Bunq\BunqClient;

final class CardResource
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
     * Lists all cards of the user.
     *
     * @param integer $userId
     *
     * @return array
     */
    public function listCards($userId)
    {
        return $this->client->get($this->getResourceEndpoint($userId));
    }

    /**
     * Gets a user its card information.
     *
     * @param integer $userId
     * @param integer $cardId
     *
     * @return array
     */
    public function getCard($userId, $cardId)
    {
        $card = $this->client->get($this->getResourceEndpoint($userId) . '/' . (int)$cardId);

        return $card['Response'][0];
    }

    /**
     * @param integer $userId
     *
     * @return string
     */
    private function getResourceEndpoint($userId)
    {
        return '/v1/user/' . (int)$userId . '/card';
    }
}
