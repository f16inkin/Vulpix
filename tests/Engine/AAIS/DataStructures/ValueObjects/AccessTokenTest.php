<?php

declare(strict_types = 1);

namespace Engine\AAIS\DataStructures\ValueObjects;

use PHPUnit\Framework\TestCase;
use Vulpix\Engine\AAIS\Domains\Tokens\AccessToken;

class AccessTokenTest extends TestCase
{
    public function testSuccess() : void {
        $token = new AccessToken($value = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9');
        self::assertEquals($value, $token->getValue());
    }

    public function testEmpty() : void
    {
        $this->expectException( \InvalidArgumentException::class);
        $token = new AccessToken('');
    }

    public function testNull() : void
    {
        $this->expectException( \InvalidArgumentException::class);
        $token = new AccessToken(null);
    }



}
