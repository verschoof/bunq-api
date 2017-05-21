<?php

namespace Bunq\Middleware;

use Bunq\Certificate\Certificate;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Dennis de Greef (original author)
 * @author Mitchel Verschoof
 */
final class ResponseSignatureMiddleware
{
    const SIGNATURE_ALGORITHM = OPENSSL_ALGO_SHA256;
    const VERIFY_IS_VALID     = 1;
    const VERIFY_IS_INVALID   = 0;
    const VERIFY_IS_ERROR     = -1;

    /**
     * @var Certificate
     */
    private $publicKey;

    /**
     * @param Certificate $publicKey
     */
    public function __construct(Certificate $publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ResponseInterface $response)
    {
        $header = $response->getHeader('X-Bunq-Server-Signature');

        if (!isset($header[0])) {
            return $response;
        }

        $decodedServerSignature = base64_decode($header[0]);
        $signatureData          = $this->createSignatureDataFromHeaders($response);

        $verify = openssl_verify(
            $signatureData,
            $decodedServerSignature,
            (string)$this->publicKey,
            self::SIGNATURE_ALGORITHM
        );

        if ($verify !== self::VERIFY_IS_VALID) {
            throw new \Exception('Server signature does not match response');
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     */
    private function createSignatureDataFromHeaders(ResponseInterface $response)
    {
        $signatureData = $response->getStatusCode();
        $signatureData .= $this->convertHeadersToSignatureData($response);
        $signatureData .= "\n\n";
        $signatureData .= (string)$response->getBody();

        return $signatureData;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     */
    private function convertHeadersToSignatureData(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        ksort($headers);

        $signatureData = '';
        foreach ($headers as $header => $values) {
            // Skip the server signature itself
            if ($header === 'X-Bunq-Server-Signature') {
                continue;
            }

            // Skip all headers that are not X-Bunq-
            if (substr($header, 0, 7) !== 'X-Bunq-') {
                continue;
            }

            // Add all header data to verify signature
            foreach ($values as $value) {
                $signatureData .= PHP_EOL . $header . ': ' . $value;
            }
        }

        return $signatureData;
    }
}
