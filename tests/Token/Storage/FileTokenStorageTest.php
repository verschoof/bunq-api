<?php

namespace Bunq\Test\Token\Storage;

use Bunq\Token\DefaultToken;
use Bunq\Token\Storage\FileTokenStorage;
use Bunq\Token\TokenType;
use PHPUnit\Framework\TestCase;

final class FileTokenStorageTest extends TestCase
{
    /**
     * @var FileTokenStorage
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new FileTokenStorage('/tmp');
    }

    /**
     * @test
     */
    public function itFindsATokenBasedOnAType()
    {
        $tokenType = TokenType::INSTALLATION_TOKEN();

        @mkdir('/tmp/tokens');
        file_put_contents('/tmp/tokens/' . $tokenType->toString(), 'tokenstring');

        $result = $this->storage->load($tokenType);

        $this->assertEquals('tokenstring', $result->toString());
    }

    /**
     * @test
     * @expectedException \Bunq\Token\Storage\TokenNotFoundException
     * @expectedExceptionMessage Could not find token "session.token" in path: /tmp/tokens
     */
    public function itThrowsAnExceptionWhenLoadingANonExistingToken()
    {
        $tokenType = TokenType::SESSION_TOKEN();

        $this->storage->load($tokenType);
    }

    /**
     * @test
     */
    public function itSavesATokenBasedOnAType()
    {
        $tokenType = TokenType::INSTALLATION_TOKEN();
        $token     = DefaultToken::fromString('newTokenData');

        $this->storage->save($token, $tokenType);

        $result = @file_get_contents('/tmp/tokens/' . $tokenType->toString());

        $this->assertSame($token->toString(), $result);
    }
}

