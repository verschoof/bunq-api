<?php

namespace Bunq\Exception;

use GuzzleHttp\Exception\ClientException;

final class BunqException extends \Exception
{
    /**
     * @var ClientException
     */
    private $exception;

    /**
     * @var array
     */
    private $responseArray;

    /**
     * @param ClientException $exception
     */
    public function __construct(ClientException $exception)
    {
        $this->exception     = $exception;
        $this->responseArray = json_decode((string) $this->exception->getResponse()->getBody(), true);

        parent::__construct(
            'Path: ' . $exception->getRequest()->getUri()->getPath() .
            ', Message: ' . (string)$exception->getResponse()->getBody(),
            $exception->getCode()
        );
    }

    /**
     * @return ClientException
     */
    public function getClientException()
    {
        return $this->exception;
    }

    /**
     * @return array
     */
    public function getResponseArray()
    {
        return $this->responseArray;
    }
}
