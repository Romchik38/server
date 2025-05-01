<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\DTO\DynamicRoute;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTO;

class DynamicRouteDTOTest extends TestCase
{
    public function testName(): void
    {
        $dto = new DynamicRouteDTO('about', 'About');
        $this->assertSame('about', $dto->name());
    }

    public function description(): void
    {
        $dto = new DynamicRouteDTO('about', 'About');
        $this->assertSame('About', $dto->description());
    }
}
