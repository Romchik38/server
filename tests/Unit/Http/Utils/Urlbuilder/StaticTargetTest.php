<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Name;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\Urlbuilder\StaticTarget;

final class StaticTargetTest extends TestCase
{
    public function testsFromPathWithRoot(): void
    {
        $path   = new Path(['root', 'article', 'news']);
        $target = new StaticTarget(new Name('en'));
        $url    = $target->fromPath($path);

        $this->assertSame('/en/article/news', $url);
    }

    public function testsFromPathWithoutRoot(): void
    {
        $path   = new Path(['en', 'article', 'news']);
        $target = new StaticTarget(new Name('en'));
        $url    = $target->fromPath($path);

        $this->assertSame('/en/article/news', $url);
    }
}
