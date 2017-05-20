<?php

namespace Bunq\Middleware;

use Bunq\Token\Token;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

final class RequestAuthenticationMiddleware
{
    /**
     * @var Token
     */
    private $sessionToken;

    /**
     * @param Token $sessionToken
     */
    public function __construct(Token $sessionToken)
    {
        $this->sessionToken = $sessionToken;
    }

    /**
     * @param RequestInterface $request
     *
     * @return Request
     */
    public function __invoke(RequestInterface $request)
    {
        $headers = $request->getHeaders();

        // Use the session token if not overridden with installation token
        if (!isset($headers['X-Bunq-Client-Authentication'])) {
            $headers['X-Bunq-Client-Authentication'] = (string)$this->sessionToken->toString();
        }

        return new Request(
            $request->getMethod(),
            $request->getUri(),
            $headers,
            $request->getBody()
        );
    }
}
