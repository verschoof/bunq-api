<?php

namespace Bunq\Service;

use Bunq\Certificate\CertificateType;
use Bunq\Certificate\DefaultCertificate;
use Bunq\Certificate\Storage\CertificateStorage;
use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\TokenNotFoundException;
use Bunq\Token\Storage\TokenStorage;
use Bunq\Token\Token;
use Bunq\Token\TokenType;

final class DefaultTokenService implements TokenService
{
    /**
     * @var InstallationService
     */
    private $installation;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var CertificateStorage
     */
    private $certificateStorage;

    /**
     * @param InstallationService $installation
     * @param TokenStorage        $tokenStorage
     * @param CertificateStorage  $certificateStorage
     */
    public function __construct(
        InstallationService $installation,
        TokenStorage $tokenStorage,
        CertificateStorage $certificateStorage
    ) {
        $this->installation       = $installation;
        $this->tokenStorage       = $tokenStorage;
        $this->certificateStorage = $certificateStorage;
    }

    /**
     * @return Token
     */
    public function sessionToken()
    {
        try {
            return $this->tokenStorage->load(TokenType::SESSION_TOKEN());
        } catch (TokenNotFoundException $exception) {
            return $this->obtainNewSessionToken();
        }
    }

    /**
     * @return Token
     */
    private function obtainNewSessionToken()
    {
        $sessionResponse = $this->installation->createSession($this->installationToken());
        $sessionToken    = DefaultToken::fromString($sessionResponse);

        $this->tokenStorage->save($sessionToken, TokenType::SESSION_TOKEN());

        return $sessionToken;
    }

    /**
     * @return Token
     */
    private function installationToken()
    {
        try {
            return $this->tokenStorage->load(TokenType::INSTALLATION_TOKEN());
        } catch (TokenNotFoundException $exception) {
            $token = $this->obtainNewInstallationToken();

            //registers the device
            $this->installation->registerDevice($token);

            return $token;
        }
    }

    /**
     * @return Token
     */
    private function obtainNewInstallationToken()
    {
        $installationResponse = $this->installation->install();

        $installationToken = DefaultToken::fromString($installationResponse['token']);
        $this->tokenStorage->save($installationToken, TokenType::INSTALLATION_TOKEN());

        $certificate = DefaultCertificate::fromString($installationResponse['public_key']);
        $this->certificateStorage->save($certificate, CertificateType::BUNQ_SERVER_KEY());

        return $installationToken;
    }
}
