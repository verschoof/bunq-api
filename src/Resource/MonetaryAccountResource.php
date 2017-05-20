<?php

namespace Bunq\Resource;

use Bunq\BunqClient;

final class MonetaryAccountResource
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
     * Lists all the Monetary accounts for the current user.
     *
     * @param integer $userId
     *
     * @return array
     */
    public function listMonetaryAccounts($userId)
    {
        $monetaryAccounts = $this->client->get($this->getResourceEndpoint($userId));

        return $monetaryAccounts;
    }

    /**
     * Gets a Monetary Account by its identifier.
     *
     * @param integer $userId
     * @param integer $id
     *
     * @return array
     */
    public function getMonetaryAccount($userId, $id)
    {
        $monetaryAccount = $this->client->get($this->getResourceEndpoint($userId) . '/' . (int)$id);

        return $monetaryAccount['Response'][0]['MonetaryAccountBank'];
    }

    /**
     * {@inheritdoc}
     */
    private function getResourceEndpoint($userId)
    {
        return '/v1/user/' . (int) $userId . '/monetary-account';
    }
}
