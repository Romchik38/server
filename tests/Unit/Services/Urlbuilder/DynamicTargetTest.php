<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Urlbuilder;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Path;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTOFactory;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\Urlbuilder\DynamicTarget;

final class DynamicTargetTest extends TestCase
{
    public function testFromPath(): void
    {
        $dynamicRoot = new DynamicRoot('en', ['en', 'uk'], new DynamicRootDTOFactory());
        $dynamicRoot->setCurrentRoot('uk');
        $path          = new Path(['root', 'products']);
        $target        = new DynamicTarget($dynamicRoot);
        $requestTarget = $target->fromPath($path);
        $this->assertSame('/uk/products', $requestTarget);
    }
}
