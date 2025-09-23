<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\Urlbuilder\RootlessTarget;

final class RootlessTargetTest extends TestCase
{
    public function testsFromPathWithRoot(): void
    {
        $path   = new Path(['article', 'news']);
        $target = new RootlessTarget();
        $url    = $target->fromPath($path);

        $this->assertSame('/article/news', $url);
    }

    public function testsFromPathWithRootWithSpecialChars(): void
    {
        $path   = new Path(['article', 'news 2025']);
        $target = new RootlessTarget();
        $url    = $target->fromPath($path);

        $this->assertSame('/article/news+2025', $url);
    }
}
