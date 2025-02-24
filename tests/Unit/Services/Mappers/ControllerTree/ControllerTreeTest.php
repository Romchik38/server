<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

use function count;
use function file_get_contents;

final class ControllerTreeTest extends TestCase
{
    public function testGetRootControllerDTO(): void
    {
        $root = (include_once __DIR__ . '/bootstrap.php')();

        $controllerTree = new ControllerTree();
        $controllerDto  = $controllerTree->getRootControllerDTO($root);

        /** 1. root */
        $this->assertSame('root', $controllerDto->getName());
        $this->assertSame('Home page', $controllerDto->getDescription());

        /** 2. root children */
        $children = $controllerDto->getChildren();
        $this->assertSame(3, count($children));

        [$products, $about, $contacts] = $children;

        $this->assertSame('products', $products->getName());
        $this->assertSame('Products catalog', $products->getDescription());

        $this->assertSame('about', $about->getName());
        $this->assertSame('About page', $about->getDescription());

        $this->assertSame('contacts', $contacts->getName());
        $this->assertSame('Contacts page', $contacts->getDescription());

        /** products children */
        $productsChildren = $products->getChildren();

        $this->assertSame(2, count($productsChildren));

        [$product1, $product2] = $productsChildren;

        $this->assertSame('product-1', $product1->getName());
        $this->assertSame('Product 1 page', $product1->getDescription());

        $this->assertSame('product-2', $product2->getName());
        $this->assertSame('Product 2 page', $product2->getDescription());
    }

    public function testGetOnlyLineRootControllerDTOWithEmptyAction()
    {
        /** @var ControllerInterface $root */
        $root             = (include_once __DIR__ . '/bootstrap2.php')();
        $controllerResult = $root->execute(['root', 'sitemap']);
        $response         = $controllerResult->getResponse();

        $expected = file_get_contents(__DIR__ . '/jsonstring');
        $this->assertSame(
            $expected,
            (string) $response->getBody()
        );
    }

    public function testGetOnlyLineRootControllerDTOWithAction()
    {
        /** @var ControllerInterface $root */
        $root             = (include_once __DIR__ . '/bootstrap3.php')();
        $controllerResult = $root->execute(['root', 'catalog', 'product-1']);
        $response         = $controllerResult->getResponse();

        $expected = file_get_contents(__DIR__ . '/jsonstring2');

        $this->assertSame(
            $expected,
            (string) $response->getBody()
        );
    }
}
