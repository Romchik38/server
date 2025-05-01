<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\DynamicRoot\DynamicRoot;
use Romchik38\Server\Http\Utils\Urlbuilder\DynamicTarget;

final class DynamicTargetTest extends TestCase
{
    public function testFromPath(): void
    {
        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');
        $path          = new Path(['root', 'products']);
        $target        = new DynamicTarget($dynamicRoot);
        $requestTarget = $target->fromPath($path);
        $this->assertSame('/uk/products', $requestTarget);
    }
}
