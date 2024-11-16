<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

class ControllerTreeTest extends TestCase
{

    protected readonly ControllerDTOInterface $controllerDTO;

    public function setUp(): void
    {
        $controllerTree = new ControllerTree();
        $root = (include_once(__DIR__ . '/bootstrap.php'))();
        $this->controllerDTO = $controllerTree->getRootControllerDTO($root);
    }

    public function testGetRootControllerDTO(): void
    {

        /** 1. root */
        $this->assertSame('root', $this->controllerDTO->getName());
        $this->assertSame('Home page', $this->controllerDTO->getDescription());

        /** 2. root children */
        $children = $this->controllerDTO->getChildren();
        $this->assertSame(3, count($children));

        [$product, $about, $contacts] = $children;

        $this->assertSame('products', $product->getName());
        $this->assertSame('products', $product->getDescription());

        $this->assertSame('about', $about->getName());
        $this->assertSame('About page', $about->getDescription());

        $this->assertSame('contacts', $contacts->getName());
        $this->assertSame('Contacts page', $contacts->getDescription());
    }
}
