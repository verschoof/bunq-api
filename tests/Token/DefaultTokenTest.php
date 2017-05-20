<?php

namespace Bunq\Test\Token;

use Bunq\Token\DefaultToken;
use Bunq\Token\Token;
use PHPUnit\Framework\TestCase;

final class DefaultTokenTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsADefaultToken()
    {
        $token = DefaultToken::fromString('string');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertSame('string', $token->toString());
        $this->assertSame('string', (string)$token);
    }
}
