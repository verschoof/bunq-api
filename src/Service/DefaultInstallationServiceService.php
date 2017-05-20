<?php

namespace Bunq\Service;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\Exception\BunqException;
use Bunq\Token\Token;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

final class DefaultInstallationServiceService implements InstallationService
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var CertificateStorage
     */
    private $certificateStorage;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var array
     */
    private $permittedIps;

    /**
     * The installation service requires a http client without the bunq middleware's
     *
     * @param ClientInterface    $httpClient
     * @param CertificateStorage $certificateStorage
     * @param string             $apiKey
     * @param array              $permittedIps
     */
    public function __construct(
        ClientInterface $httpClient,
        CertificateStorage $certificateStorage,
        $apiKey,
        array $permittedIps
    ) {
        $this->httpClient         = $httpClient;
        $this->certificateStorage = $certificateStorage;
        $this->apiKey             = (string)$apiKey;
        $this->permittedIps       = $permittedIps;
    }

    /**
     * Registers your public key with the Bunq API.
     *
     * @return array
     */
    public function install()
    {
        $publicKey = $this->certificateStorage->load(CertificateType::PUBLIC_KEY());

        $response = $this->sendInstallationPostRequest(
            '/v1/installation',
            [
                'json' => [
                    'client_public_key' => $publicKey->toString(),
                ],
            ]
        );

        $responseArray = \json_decode((string)$response->getBody(), true);

        return [
            'token'      => $responseArray['Response'][1]['Token']['token'],
            'public_key' => $responseArray['Response'][2]['ServerPublicKey']['server_public_key'],
        ];
    }

    /**
     * Registers a device with the Bunq API.
     *
     * @param Token $token
     *
     * return void
     */
    public function registerDevice(Token $token)
    {
        $this->sendInstallationPostRequest(
            '/v1/device-server',
            [
                'headers' => [
                    'X-Bunq-Client-Authentication' => $token->toString(),
                ],
                'json'    => [
                    'description'   => 'Bunq PHP API Client',
                    'secret'        => $this->apiKey,
                    'permitted_ips' => $this->permittedIps,
                ],
            ]
        );
    }

    /**
     * Registers a session with the Bunq API.
     *
     * @param Token $token
     *
     * @return array
     */
    public function createSession(Token $token)
    {
        $response = $this->sendInstallationPostRequest(
            '/v1/session-server',
            [
                'headers' => [
                    'X-Bunq-Client-Authentication' => $token->toString(),
                ],
                'json'    => [
                    'secret' => $this->apiKey,
                ],
            ]
        );

        $responseArray = \json_decode((string)$response->getBody(), true);

        return $responseArray['Response'][1]['Token']['token'];
    }

    /**
     * Sends a post request using the installation HTTP Client
     *
     * @param       $url
     * @param array $options
     *
     * @return ResponseInterface
     * @throws BunqException
     */
    private function sendInstallationPostRequest($url, array $options = [])
    {
        try {
            return $this->httpClient->request('POST', $url, $options);
        } catch (ClientException $exception) {
            throw new BunqException($exception);
        }
    }
}
