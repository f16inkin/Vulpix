<?php

namespace Engine\AAIS\Domains;

use Vulpix\Engine\AAIS\Domains\Authentication\Authentication;
use PHPUnit\Framework\TestCase;
use Vulpix\Engine\AAIS\Service\RTCreator;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Database\Connectors\MySQLConnector;

class AuthenticationTest extends TestCase
{
    public function testAuthenticateSuccess() : void
    {
        $connector = new MySQLConnector();
        $authentication = new Authentication($connector, new RTCreator($connector), new HttpResultContainer());
        $result = $authentication->authenticate('Mikki', '12345');
        self::assertEquals(200, $result->getStatus());

    }

    public function testWrongPass() : void
    {
        $connector = new MySQLConnector();
        $authentication = new Authentication($connector, new RTCreator($connector), new HttpResultContainer());
        $result = $authentication->authenticate('Mikki', '123456');
        self::assertEquals(403, $result->getStatus());
        self::assertEquals('Пароль не верен.', $result->getBody());
    }

    public function testWrongAccount() : void
    {
        $connector = new MySQLConnector();
        $authentication = new Authentication($connector, new RTCreator($connector), new HttpResultContainer());
        $result = $authentication->authenticate('Mikki1', '12345');
        self::assertEquals(403, $result->getStatus());
        self::assertEquals('Такой учетной записи не существует в системе', $result->getBody());
    }

    public function testFindAccountSuccess() : void
    {
        $connector = new MySQLConnector();
        $class = new \ReflectionClass(Authentication::class);
        $method = $class->getMethod('findAccount');
        $method->setAccessible(true);
        $obj = new Authentication($connector, new RTCreator($connector), new HttpResultContainer());

        $result = $method->invoke($obj, 'Mikki');
        $this->assertIsArray($result);
    }

    public function testFindAccountFail() : void
    {
        $connector = new MySQLConnector();
        $class = new \ReflectionClass(Authentication::class);
        $method = $class->getMethod('findAccount');
        $method->setAccessible(true);
        $obj = new Authentication($connector, new RTCreator($connector), new HttpResultContainer());

        $result = $method->invoke($obj, 'Mikki1');
        $this->assertEquals(false, $result);
    }
}