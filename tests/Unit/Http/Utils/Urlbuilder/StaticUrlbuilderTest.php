<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\Urlbuilder\StaticUrlbuilder;

final class StaticUrlbuilderTest extends TestCase
{
    public function testWithRootPathWithRoot(): void
    {
        $path       = new Path(['root', 'article', 'views']);
        $urlbuilder = new StaticUrlbuilder($path);
        $root       = 'en';
        $url        = $urlbuilder->withRoot($root);

        $this->assertSame('/en/article/views', $url);
    }

    public function testWithRootPathWithoutRoot(): void
    {
        $path       = new Path(['article', 'views']);
        $urlbuilder = new StaticUrlbuilder($path);
        $root       = 'en';
        $url        = $urlbuilder->withRoot($root);

        $this->assertSame('/en/article/views', $url);
    }
}
