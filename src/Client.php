<?php

namespace Bunq;

use Bunq\Exception\BunqException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

final class Client implements BunqClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function get($url, array $options = [])
    {
        return $this->requestAPI('GET', (string)$url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function post($url, array $options = [])
    {
        return $this->requestAPI('POST', (string)$url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    public function put($url, array $options = [])
    {
        return $this->requestAPI('PUT', $url, $options);
    }

    /**
     * Handles the API Calling.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return array
     * @throws BunqException
     */
    private function requestAPI($method, $url, array $options = [])
    {
        try {
            $response = $this->httpClient->request((string)$method, $url, $options);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            throw new BunqException($e);
        }
    }
}
