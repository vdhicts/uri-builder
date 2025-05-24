<?php

namespace Vdhicts\UriBuilder\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Vdhicts\HttpQueryBuilder\Parameter;
use Vdhicts\UriBuilder\BasicAuthentication;
use Vdhicts\UriBuilder\Uri;

class UriTest extends TestCase
{
    public function testRelativeUri(): void
    {
        $uri = new Uri(
            paths: ['path1', 'path2'],
            queryParameters: [
                new Parameter('key1', 'value1'),
                new Parameter('key2', 'value2'),
            ],
            fragment: 'fragment',
        );

        $this->assertSame('/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);
    }

    public function testAbsoluteUri(): void
    {
        $uri = new Uri(
            scheme: 'https',
            subdomain: 'sub',
            domain: 'example',
            topLevelDomain: 'com',
            port: 443,
            paths: ['path1', 12],
            queryParameters: [
                new Parameter('key1', 'value1'),
                new Parameter('key2', 'value2'),
            ],
            fragment: 'fragment',
            basicAuthentication: new BasicAuthentication('username', 'password'),
        );

        $this->assertSame('https://username:password@sub.example.com:443/path1/12?key1=value1&key2=value2#fragment', (string) $uri);
    }

    public function testMutatingUri(): void
    {
        $uri = new Uri(
            scheme: 'https',
            subdomain: 'sub',
            domain: 'example',
            topLevelDomain: 'com',
            port: 443,
            paths: ['path1', 'path2'],
            queryParameters: [
                new Parameter('key1', 'value1'),
                new Parameter('key2', 'value2'),
            ],
            fragment: 'fragment',
            basicAuthentication: new BasicAuthentication('username', 'password'),
        );

        $uri->setScheme('http');

        $this->assertSame('http://username:password@sub.example.com:443/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->setSubdomain('newsub');

        $this->assertSame('http://username:password@newsub.example.com:443/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->setDomain('newdomain');

        $this->assertSame('http://username:password@newsub.newdomain.com:443/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->setTopLevelDomain('net');

        $this->assertSame('http://username:password@newsub.newdomain.net:443/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->setPort(8080);

        $this->assertSame('http://username:password@newsub.newdomain.net:8080/path1/path2?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->addPath('path3');

        $this->assertSame('http://username:password@newsub.newdomain.net:8080/path1/path2/path3?key1=value1&key2=value2#fragment', (string) $uri);

        $uri->addQueryParameter(new Parameter('key2', 'value3'));

        $this->assertSame('http://username:password@newsub.newdomain.net:8080/path1/path2/path3?key1=value1&key2=value2&key2=value3#fragment', (string) $uri);

        $uri->setFragment('newfragment');

        $this->assertSame('http://username:password@newsub.newdomain.net:8080/path1/path2/path3?key1=value1&key2=value2&key2=value3#newfragment', (string) $uri);

        $uri->setBasicAuthentication(new BasicAuthentication('newuser', 'newpassword'));

        $this->assertSame('http://newuser:newpassword@newsub.newdomain.net:8080/path1/path2/path3?key1=value1&key2=value2&key2=value3#newfragment', (string) $uri);
    }

    public function testInvalidPort(): void
    {
        $uri = new Uri();

        $this->expectException(InvalidArgumentException::class);

        $uri->setPort(70000);
    }

    public function testInvalidPaths(): void
    {
        $uri = new Uri();

        $this->expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        $uri->setPaths([true, ['test']]);
    }

    public function testInvalidQueryParameters(): void
    {
        $uri = new Uri();

        $this->expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        $uri->setQueryParameters(['test']);
    }
}
