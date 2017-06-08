<?php

namespace Bunq\Exception;

final class SessionWasExpiredException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Session has expired should now be refreshed', 400);
    }
}
