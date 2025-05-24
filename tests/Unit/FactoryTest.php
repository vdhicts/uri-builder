<?php

namespace Vdhicts\UriBuilder\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Vdhicts\UriBuilder\Factory;

class FactoryTest extends TestCase
{
    private Factory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new Factory();
    }

    public function testBuildingRelativeUri(): void
    {
        $uri = $this
            ->factory
            ->build('/path1/path2?key1=value1&key2=value2#fragment');

        $this->assertSame('path1', $uri->getPaths()[0]);
        $this->assertSame('path2', $uri->getPaths()[1]);
        $this->assertSame('key1', $uri->getQueryParameters()[0]->key);
        $this->assertSame('value1', $uri->getQueryParameters()[0]->value);
        $this->assertSame('key2', $uri->getQueryParameters()[1]->key);
        $this->assertSame('value2', $uri->getQueryParameters()[1]->value);
        $this->assertSame('fragment', $uri->getFragment());
    }

    public function testBuildingAbsoluteUri(): void
    {
        $uri = $this
            ->factory
            ->build('https://user:pass@sub.example.com:8080/path1/path2?key1=value1&key2=value2#fragment');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('user', $uri->getBasicAuthentication()->username);
        $this->assertSame('pass', $uri->getBasicAuthentication()->password);
        $this->assertSame('sub', $uri->getSubdomain());
        $this->assertSame('example', $uri->getDomain());
        $this->assertSame('com', $uri->getTopLevelDomain());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('path1', $uri->getPaths()[0]);
        $this->assertSame('path2', $uri->getPaths()[1]);
        $this->assertSame('key1', $uri->getQueryParameters()[0]->key);
        $this->assertSame('value1', $uri->getQueryParameters()[0]->value);
        $this->assertSame('key2', $uri->getQueryParameters()[1]->key);
        $this->assertSame('value2', $uri->getQueryParameters()[1]->value);
        $this->assertSame('fragment', $uri->getFragment());
    }
}
