<?php

namespace Bunq\Service;

use Bunq\Token\Token;

interface InstallationService
{
    /**
     * Registers your public key with the Bunq API.
     *
     * @return array
     */
    public function install();

    /**
     * Registers a device with the Bunq API.
     *
     * @param Token $token
     *
     * return void
     */
    public function registerDevice(Token $token);

    /**
     * Registers a session with the Bunq API.
     *
     * @param Token $token
     *
     * @return array
     */
    public function createSession(Token $token);
}
