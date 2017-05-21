<?php

namespace Bunq\Middleware;

use Bunq\Certificate\Certificate;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * @author Dennis de Greef (original author)
 * @author Mitchel Verschoof
 */
final class RequestSignatureMiddleware
{
    const SIGNATURE_ALGORITHM = OPENSSL_ALGO_SHA256;

    /**
     * @var Certificate
     */
    private $privateKey;

    /**
     * @param Certificate $privateKey
     */
    public function __construct(Certificate $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return Request
     */
    public function __invoke(RequestInterface $request, array $options = [])
    {
        $signature = $this->createSignature($request);

        $headers = $request->getHeaders();
        ksort($headers);
        $headers['X-Bunq-Client-Signature'] = base64_encode($signature);

        return new Request(
            $request->getMethod(),
            $request->getUri(),
            $headers,
            $request->getBody()
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function createSignature(RequestInterface $request)
    {
        $headers = $request->getHeaders();
        ksort($headers);

        $signatureData = $request->getMethod() . ' ' . $request->getRequestTarget();
        $signatureData .= $this->createSignatureDataFromHeaders($headers);
        $signatureData .= "\n\n";

        $body = (string)$request->getBody();
        if (!empty($body)) {
            $signatureData .= $body;
        }

        return $this->sign($signatureData);
    }

    /**
     * @param array $headers
     *
     * @return string
     */
    private function createSignatureDataFromHeaders(array $headers)
    {
        $signatureData = '';

        foreach ($headers as $header => $values) {
            foreach ($values as $value) {
                if ($header === 'User-Agent'
                    || $header === 'Cache-Control'
                    || substr($header, 0, 7) === 'X-Bunq-'
                ) {
                    $signatureData .= PHP_EOL . $header . ': ' . $value;
                }
            }
        }

        return $signatureData;
    }

    /**
     * @param string $data
     *
     * @return string
     * @throws \Exception
     */
    private function sign($data)
    {
        if (openssl_sign((string)$data, $signature, $this->privateKey->toString(), static::SIGNATURE_ALGORITHM) !== true) {
            throw new \Exception("Could not sign request: " . openssl_error_string());
        }

        return $signature;
    }
}
