<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\Breadcrumb;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbDTO;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbDTOInterface;

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
