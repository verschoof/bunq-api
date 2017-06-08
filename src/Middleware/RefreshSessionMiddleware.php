<?php

namespace Bunq\Middleware;

use Bunq\Exception\SessionWasExpiredException;
use Bunq\Service\InstallationService;
use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\TokenStorage;
use Bunq\Token\TokenType;
use Psr\Http\Message\ResponseInterface;

/**
 * bunq documentation:
 * A session expires after the same amount of time you have set for auto logout in your user account. If a request is
 * made 30 seconds before a session expires, it will automatically be extended.
 *
 * This middleware detects if the session has expired, if so, the middleware will renew the session and throws an
 * exception to let the client know it can be retried.
 */
final class RefreshSessionMiddleware
{
    /**
     * @var InstallationService
     */
    private $installationService;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @param InstallationService $installationService
     * @param TokenStorage        $tokenStorage
     */
    public function __construct(
        InstallationService $installationService,
        TokenStorage $tokenStorage
    ) {
        $this->installationService = $installationService;
        $this->tokenStorage        = $tokenStorage;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== 401) {
            return $response;
        }

        $responseBody = \json_decode($response->getBody()->__toString(), true);

        if ($responseBody['Error'][0]['error_description'] !== 'Insufficient authorisation.') {
            return $response;
        }

        // make new session

        $installationToken = $currentInstalltionToken = $this->tokenStorage->load(TokenType::INSTALLATION_TOKEN());
        $session           = $this->installationService->createSession($installationToken);

        $sessionToken = DefaultToken::fromString($session);

        $this->tokenStorage->save($sessionToken, TokenType::SESSION_TOKEN());

        throw new SessionWasExpiredException();
    }

}
