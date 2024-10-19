<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOInterface;
use Romchik38\Server\Models\DTO\Http\Breadcrumb\BreadcrumbDTO;

class BreadcrumbDTOTest extends TestCase
{
    protected BreadcrumbDTOInterface $root;
    protected BreadcrumbDTOInterface $about;
    protected $name = 'Home Page';
    protected $description = 'Home description';
    protected $url = '/';


    public function setUp(): void
    {
        $this->root = new BreadcrumbDTO(
            $this->name,
            $this->description,
            $this->url,
            null
        );

        $this->about = new BreadcrumbDTO(
            $this->name,
            $this->description,
            $this->url,
            $this->root
        );
    }

    public function testGetName()
    {
        $this->assertSame($this->name, $this->about->getName());
    }

    public function testGetDescription()
    {
        $this->assertSame($this->description, $this->about->getDescription());
    }

    public function testGetUrl()
    {
        $this->assertSame($this->url, $this->about->getUrl());
    }

    public function testGetPrev()
    {
        $this->assertSame($this->root, $this->about->getPrev());
    }
}
