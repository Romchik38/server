<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Urlbuilder;

use InvalidArgumentException;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Path;
use Romchik38\Server\Services\Urlbuilder\Target;
use Romchik38\Server\Services\Urlbuilder\Urlbuilder;

final class UrlbuilderTest extends TestCase
{
    public function testFromPath(): void
    {
        $request = (new Request())
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
        $request    = new Request();
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
        $request    = new Request();
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
        $request = new Request();

        $url = new Urlbuilder(
            $request,
            new Target()
        );

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path);
        $this->assertSame('/product', $uri);
    }
}
