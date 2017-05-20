<?php

namespace Bunq\Test\Token;

use Bunq\Token\TokenType;
use PHPUnit\Framework\TestCase;

final class TokenTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itRepresentsATokenType()
    {
        $token = TokenType::INSTALLATION_TOKEN();

        $this->assertInstanceOf(TokenType::class, $token);
        $this->assertEquals('installation.token', $token->toString());
        $this->assertEquals('installation.token', (string)$token);

        $this->assertTrue($token->equals(TokenType::INSTALLATION_TOKEN()));
        $this->assertFalse($token->equals(TokenType::SESSION_TOKEN()));
        $this->assertFalse($token->equals(new \stdClass()));
        $this->assertFalse($token->equals('class'));
    }

    /**
     * @test
     */
    public function itGiveAllTypesOfAToken()
    {
        $tokenTypes = TokenType::all();

        $expectedTokens = [
            TokenType::INSTALLATION_TOKEN(),
            TokenType::SESSION_TOKEN(),
        ];

        $this->assertEquals($expectedTokens, $tokenTypes);
    }

    /**
     * @test
     */
    public function itGiveAllTypesOfATokenAsString()
    {
        $tokenTypes = TokenType::allAsString();

        $expectedTokens = [
            TokenType::INSTALLATION_TOKEN,
            TokenType::SESSION_TOKEN,
        ];

        $this->assertEquals($expectedTokens, $tokenTypes);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid token type "unknownType"
     */
    public function itCannotBeCreatedWithAUnknownType()
    {
        TokenType::fromString('unknownType');
    }
}
