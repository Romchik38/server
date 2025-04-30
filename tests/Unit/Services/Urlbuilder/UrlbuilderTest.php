<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Urlbuilder;

use InvalidArgumentException;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Services\Urlbuilder\Target;
use Romchik38\Server\Services\Urlbuilder\Urlbuilder;

final class UrlbuilderTest extends TestCase
{
    public function testFromPath(): void
    {
        $request = (new ServerRequest())
            ->withUri(new Uri('http://example.com'))
            ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path);
        $this->assertSame('http://example.com/product', $uri);
    }

    public function testFromPathEmptyAuthority(): void
    {
        $request    = new ServerRequest();
        $requestUri = $request->getUri()->withScheme('http');
        $request    = $request->withUri($requestUri);

        $this->expectException(InvalidArgumentException::class);
        new Urlbuilder(
            $request,
            new Target()
        );
    }

    public function testFromPathEmptyScheme(): void
    {
        $request    = new ServerRequest();
        $requestUri = $request->getUri()->withHost('example.com');
        $request    = $request->withUri($requestUri);

        $this->expectException(InvalidArgumentException::class);
        new Urlbuilder(
            $request,
            new Target()
        );
    }

    public function testFromPathEmptyAuthorityAndScheme(): void
    {
        $request = new ServerRequest();

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path);
        $this->assertSame('/product', $uri);
    }

    public function testFromPathWithParam(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1']);
        $this->assertSame('http://example.com/product?id=1', $uri);
    }

    public function testFromPathWithParams(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1', 'color' => 'red']);
        $this->assertSame('http://example.com/product?id=1&color=red', $uri);
    }

    public function testFromPathWithFragment(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, [], 'fregment1');
        $this->assertSame('http://example.com/product#fregment1', $uri);
    }

    public function testFromPathWithParamsAndFragment(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1', 'color' => 'red'], 'fr1');
        $this->assertSame('http://example.com/product?id=1&color=red#fr1', $uri);
    }

    public function testFromArray(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $uri = $url->fromArray(['root', 'product']);
        $this->assertSame('http://example.com/product', $uri);
    }

    public function testFromArrayWithParams(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $uri = $url->fromArray(
            ['root', 'product'],
            ['id' => '1', 'color' => 'red']
        );
        $this->assertSame('http://example.com/product?id=1&color=red', $uri);
    }

    public function testFromArrayWithFragment(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $uri = $url->fromArray(['root', 'product'], [], 'fregment1');
        $this->assertSame('http://example.com/product#fregment1', $uri);
    }

    public function testFromArrayWithParamsAndFragment(): void
    {
        $request = (new ServerRequest())
        ->withUri(new Uri('http://example.com'))
        ->withMethod('GET');

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $uri = $url->fromArray(
            ['root', 'product'],
            ['id' => '1', 'color' => 'red'],
            'fr1'
        );
        $this->assertSame('http://example.com/product?id=1&color=red#fr1', $uri);
    }
}
