<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Urlbuilder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\Urlbuilder\Urlbuilder;

final class UrlbuilderTest extends TestCase
{
    public function testFromPath(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path);
        $this->assertSame('http://example.com/product', $uri);
    }

    public function testFromPathEmptyAuthority(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Urlbuilder('http');
    }

    public function testFromPathEmptyScheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Urlbuilder('', 'example.com');
    }

    public function testFromPathEmptyAuthorityAndScheme(): void
    {
        $url = new Urlbuilder();

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path);
        $this->assertSame('/product', $uri);
    }

    public function testFromPathWithParam(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1']);
        $this->assertSame('http://example.com/product?id=1', $uri);
    }

    public function testFromPathWithParams(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1', 'color' => 'red']);
        $this->assertSame('http://example.com/product?id=1&color=red', $uri);
    }

    public function testFromPathWithFragment(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, [], 'fregment1');
        $this->assertSame('http://example.com/product#fregment1', $uri);
    }

    public function testFromPathWithParamsAndFragment(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $path = new Path(['root', 'product']);

        $uri = $url->fromPath($path, ['id' => '1', 'color' => 'red'], 'fr1');
        $this->assertSame('http://example.com/product?id=1&color=red#fr1', $uri);
    }

    public function testFromArray(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $uri = $url->fromArray(['root', 'product']);
        $this->assertSame('http://example.com/product', $uri);
    }

    public function testFromArrayWithParams(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $uri = $url->fromArray(
            ['root', 'product'],
            ['id' => '1', 'color' => 'red']
        );
        $this->assertSame('http://example.com/product?id=1&color=red', $uri);
    }

    public function testFromArrayWithFragment(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $uri = $url->fromArray(['root', 'product'], [], 'fregment1');
        $this->assertSame('http://example.com/product#fregment1', $uri);
    }

    public function testFromArrayWithParamsAndFragment(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $uri = $url->fromArray(
            ['root', 'product'],
            ['id' => '1', 'color' => 'red'],
            'fr1'
        );
        $this->assertSame('http://example.com/product?id=1&color=red#fr1', $uri);
    }

    public function testFromArrayWithSpecialChars(): void
    {
        $url = new Urlbuilder('http', 'example.com');

        $uri = $url->fromArray(
            ['root', 'product'],
            ['id' => '1', 'color' => 'red&blue'] // %26
        );
        $this->assertSame('http://example.com/product?id=1&color=red%26blue', $uri);
    }
}
