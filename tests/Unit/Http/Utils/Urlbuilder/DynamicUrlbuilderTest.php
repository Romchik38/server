<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;
use Romchik38\Server\Http\Utils\Urlbuilder\DynamicUrlbuilder;

final class DynamicUrlbuilderTest extends TestCase
{
    public function testWithDefaults(): void
    {
        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');
        $path = ['root', 'article', 'views'];
        $du   = new DynamicUrlbuilder($dynamicRoot);

        $this->assertSame('/uk/article/views', $du->fromArray($path));
    }

    public function testWithDefaultsWithSpecialChars(): void
    {
        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');
        $path = ['root', 'article', 'views 2025'];
        $du   = new DynamicUrlbuilder($dynamicRoot);

        $this->assertSame('/uk/article/views+2025', $du->fromArray($path));
    }

    public function testWithSchema(): void
    {
        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');
        $path = ['root', 'article', 'views'];
        $du   = new DynamicUrlbuilder($dynamicRoot, 'http', 'example.com');

        $this->assertSame('http://example.com/uk/article/views', $du->fromArray($path));
    }
}
