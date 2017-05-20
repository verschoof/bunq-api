<?php

namespace Bunq\Middleware;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Ramsey\Uuid\Uuid;

final class RequestIdMiddleware
{
    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return Request
     */
    public function __invoke(RequestInterface $request, array $options = [])
    {
        $requestId = Uuid::uuid4()->toString();
        if (isset($options['request-id'])) {
            $requestId = $options['request-id'];
        }

        $headers                               = $request->getHeaders();
        $headers['X-Bunq-Client-Request-Id'][] = $requestId;
        $headers['Cache-Control'][]            = 'no-cache';
        $headers['X-Bunq-Geolocation'][]       = '52.3 4.89 12 100 NL';
        $headers['X-Bunq-Language'][]          = 'nl_NL';
        $headers['X-Bunq-Region'][]            = 'nl_NL';

        return new Request(
            $request->getMethod(),
            $request->getUri(),
            $headers,
            $request->getBody()
        );
    }
}
