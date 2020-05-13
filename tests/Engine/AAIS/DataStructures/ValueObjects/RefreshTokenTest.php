<?php

declare(strict_types = 1);

namespace Engine\AAIS\DataStructures\ValueObjects;

use Vulpix\Engine\AAIS\DataStructures\ValueObjects\RefreshToken;
use PHPUnit\Framework\TestCase;

class RefreshTokenTest extends TestCase
{
    public function testSuccess() : void {
        $token = new RefreshToken($value = '25480255-8282-45d2-989f-67e679584876');
        self::assertEquals($value, $token->getValue());
    }

    public function testEmpty() : void
    {
        $this->expectException( \InvalidArgumentException::class);
        $token = new RefreshToken('');
    }

    public function testNull() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $token = new RefreshToken(null);
    }
}
