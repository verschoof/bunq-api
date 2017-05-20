<?php

namespace Bunq;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\Middleware\RequestAuthenticationMiddleware;
use Bunq\Middleware\RequestIdMiddleware;
use Bunq\Middleware\RequestSignatureMiddleware;
use Bunq\Middleware\ResponseSignatureMiddleware;
use Bunq\Service\TokenService;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

final class HttpClientFactory
{
    /**
     * Creates an installation client
     *
     * @param string             $url
     * @param CertificateStorage $certificateStorage
     *
     * @return ClientInterface
     */
    public static function createInstallationClient($url, CertificateStorage $certificateStorage)
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(
            Middleware::mapRequest(new RequestIdMiddleware())
        );
        $handlerStack->push(
            Middleware::mapRequest(new RequestSignatureMiddleware($certificateStorage->load(CertificateType::PRIVATE_KEY())))
        );

        return self::createBaseClient((string)$url, $handlerStack);
    }

    /**
     * Creates the HttpClient with all handlers
     *
     * @param string             $url
     * @param TokenService       $tokenService
     * @param CertificateStorage $certificateStorage
     *
     * @return ClientInterface
     */
    public static function create($url, TokenService $tokenService, CertificateStorage $certificateStorage)
    {
        $sessionToken    = $tokenService->sessionToken();
        $publicServerKey = $certificateStorage->load(CertificateType::BUNQ_SERVER_KEY());

        $handlerStack = HandlerStack::create();
        $handlerStack->push(
            Middleware::mapRequest(new RequestIdMiddleware())
        );
        $handlerStack->push(
            Middleware::mapRequest(new RequestAuthenticationMiddleware($sessionToken))
        );
        $handlerStack->push(
            Middleware::mapRequest(new RequestSignatureMiddleware($certificateStorage->load(CertificateType::PRIVATE_KEY())))
        );
        $handlerStack->push(
            Middleware::mapResponse(new ResponseSignatureMiddleware($publicServerKey))
        );

        $httpClient = self::createBaseClient($url, $handlerStack);

        return $httpClient;
    }

    /**
     * Returns the standard used headers.
     *
     * @param string            $url
     * @param HandlerStack|null $handlerStack
     *
     * @return ClientInterface
     */
    private static function createBaseClient($url, HandlerStack $handlerStack = null)
    {
        $httpClient = new \GuzzleHttp\Client(
            [
                'base_uri' => $url,
                'handler'  => $handlerStack,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'User-Agent'   => 'bunq-api-client:user',
                ],
            ]
        );

        return $httpClient;
    }
}
