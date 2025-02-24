<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\DTO\Http\Link;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOInterface;
use Romchik38\Server\Models\DTO\Http\Link\LinkDTO;

class LinkDTOTest extends TestCase
{
    protected LinkDTOInterface $linkDTO;
    protected string $name = 'Home';
    protected string $description = 'Home Page';
    protected string $url = '/en';

    public function setUp(): void
    {
        $this->linkDTO = new LinkDTO(
            $this->name,
            $this->description,
            $this->url,
        );
    }

    public function testGetName()
    {
        $this->assertSame($this->name, $this->linkDTO->getName());
    }

    public function testGetDescription()
    {
        $this->assertSame($this->description, $this->linkDTO->getDescription());
    }

    public function testGetUrl()
    {
        $this->assertSame($this->url, $this->linkDTO->getUrl());
    }
}
