<?php

namespace Bunq\Resource;

use Bunq\BunqClient;

final class PaymentResource
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
     * Lists all payments.
     *
     * @param integer $userId
     * @param integer $monetaryAccountId
     *
     * @return array
     */
    public function listPayments($userId, $monetaryAccountId)
    {
        $payments = $this->client->get($this->getResourceEndpoint($userId, $monetaryAccountId));
        foreach ($payments['Response'] as $key => $payment) {
            $payments['Response'][$key]['Payment']['amount']['value'] = $this->floatToCents($payment['Payment']['amount']['value']);
        }

        return $payments;
    }

    /**
     * Gets a user its payment information.
     *
     * @param integer $userId
     * @param integer $monetaryAccountId
     * @param integer $id
     *
     * @return array
     */
    public function getPayment($userId, $monetaryAccountId, $id)
    {
        $paymentResponse = $this->client->get($this->getResourceEndpoint($userId, $monetaryAccountId) . '/' . (int)$id);

        $payment = $paymentResponse['Response'][0]['Payment'];

        $payment['amount']['value'] = $this->floatToCents($payment['amount']['value']);

        return $payment;
    }

    /**
     * @param integer $userId
     * @param integer $monetaryAccountId
     *
     * @return string
     */
    private function getResourceEndpoint($userId, $monetaryAccountId)
    {
        return '/v1/user/' . (int)$userId . '/monetary-account/' . (int)$monetaryAccountId . '/payment';
    }

    /**
     * @param float $amount
     *
     * @return integer
     */
    private function floatToCents($amount)
    {
        return bcmul($amount, 100);
    }
}
