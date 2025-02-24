<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

class ControllerTreeTest extends TestCase
{

    public function testGetRootControllerDTO(): void
    {
        $root = (include_once(__DIR__ . '/bootstrap.php'))();

        $controllerTree = new ControllerTree();
        $controllerDTO = $controllerTree->getRootControllerDTO($root);

        /** 1. root */
        $this->assertSame('root', $controllerDTO->getName());
        $this->assertSame('Home page', $controllerDTO->getDescription());

        /** 2. root children */
        $children = $controllerDTO->getChildren();
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
        $root = (include_once(__DIR__ . '/bootstrap2.php'))();
        $controllerResult = $root->execute(['root', 'sitemap']);
        $response = $controllerResult->getResponse();

        $this->assertSame(
            '{"name":"root","path":[],"children":[{"name":"sitemap","path":["root"],"children":[],"description":"Sitemap page"}],"description":"Home page"}', 
            (string) $response->getBody()
        );
    }

    public function testGetOnlyLineRootControllerDTOWithAction()
    {
        /** @var ControllerInterface $root */
        $root = (include_once(__DIR__ . '/bootstrap3.php'))();
        $controllerResult = $root->execute(['root', 'catalog', 'product-1']);
        $encodedTree = $controllerResult->getResponse();
        $response = $controllerResult->getResponse();

        $this->assertSame(
            '{"name":"root","path":[],"children":[{"name":"catalog","path":["root"],"children":[{"name":"product-1","path":["root","catalog"],"children":[],"description":"Product 1 page"}],"description":"Products catalog"}],"description":"Home page"}', 
            (string) $response->getBody()
        );
    }
}
