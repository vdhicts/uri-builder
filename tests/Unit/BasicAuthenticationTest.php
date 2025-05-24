<?php

namespace Vdhicts\UriBuilder\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Vdhicts\UriBuilder\BasicAuthentication;

class BasicAuthenticationTest extends TestCase
{
    public function testInitialisation(): void
    {
        $basicAuthentication = new BasicAuthentication('username', 'password');
        $this->assertEquals('username', $basicAuthentication->username);
        $this->assertEquals('password', $basicAuthentication->password);
    }

    public function testToString(): void
    {
        $basicAuthentication = new BasicAuthentication('username', 'password');
        $this->assertEquals('username:password', (string) $basicAuthentication);
        $this->assertEquals('username:password', $basicAuthentication->toString());
    }
}
