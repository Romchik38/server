<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

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
