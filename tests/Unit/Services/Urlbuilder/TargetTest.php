<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Path;
use Romchik38\Server\Services\Urlbuilder\Target;

final class TargetTest extends TestCase
{
    public function testFromPath(): void
    {
        $path          = new Path(['root', 'products']);
        $target        = new Target();
        $requestTarget = $target->fromPath($path);
        $this->assertSame('/products', $requestTarget);
    }
}
