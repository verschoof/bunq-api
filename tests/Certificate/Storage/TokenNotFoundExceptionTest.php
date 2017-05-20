<?php

namespace Bunq\Test\Certificate\Storage;

use Bunq\Token\Storage\TokenNotFoundException;
use Bunq\Token\TokenType;
use PHPUnit\Framework\TestCase;

final class TokenNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsATokenNotFoundException()
    {
        $exception = new TokenNotFoundException(TokenType::INSTALLATION_TOKEN(), '/tmp/tokens');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Could not find token "installation.token" in path: /tmp/tokens', $exception->getMessage());
    }
}

