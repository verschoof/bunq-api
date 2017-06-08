<?php

namespace Bunq\Tests\Exception;

use Bunq\Exception\SessionWasExpiredException;
use PHPUnit\Framework\TestCase;

final class SessionWasExpiredExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsASessionWasExpiredException()
    {
        $sessionWasExpiredException = new SessionWasExpiredException();

        $this->assertInstanceOf(SessionWasExpiredException::class, $sessionWasExpiredException);
        $this->assertInstanceOf(\Exception::class, $sessionWasExpiredException);
        $this->assertSame('Session has expired should now be refreshed', $sessionWasExpiredException->getMessage());
        $this->assertSame(400, $sessionWasExpiredException->getCode());
    }
}

