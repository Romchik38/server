<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOInterface;
use Romchik38\Server\Models\DTO\Http\Breadcrumb\BreadcrumbDTO;

class BreadcrumbDTOTest extends TestCase
{
    protected BreadcrumbDTOInterface $root;
    protected BreadcrumbDTOInterface $about;

    public function setUp(): void
    {
        $this->root = new BreadcrumbDTO(
            'home',
            'Home page',
            '/',
            null
        );

        $this->about = new BreadcrumbDTO(
            'about',
            'About page',
            '/about',
            $this->root
        );
    }

    public function testGetName()
    {
        $this->assertSame('about', $this->about->getName());
    }

    public function testGetDescription()
    {
        $this->assertSame('About page', $this->about->getDescription());
    }

    public function testGetUrl()
    {
        $this->assertSame('/about', $this->about->getUrl());
    }

    public function testGetPrev()
    {
        $this->assertSame($this->root, $this->about->getPrev());
    }
}
