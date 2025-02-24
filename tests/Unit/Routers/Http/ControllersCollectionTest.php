<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Routers\Http;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Routers\Http\ControllersCollection;

class ControllersCollectionTest extends TestCase
{
    protected Controller $controller;
    protected $controllerName = 'root';
    protected ControllersCollection $collection;
    protected $method = 'GET';

    public function setUp(): void
    {
        $this->controller = new Controller($this->controllerName);
        $this->collection = new ControllersCollection();
        $this->collection->setController($this->controller, $this->method);
    }

    public function testGetController()
    {
        $controller = $this->collection->getController($this->method);
        $this->assertSame($this->controllerName, $controller->getName());
    }

    /** 
     * public function testSetController(){}
     * 
     * is tested by setUp() and testGetController()
     */
    public function testGetMethods()
    {
        $methods = $this->collection->getMethods();
        $this->assertSame([$this->method], $methods);
    }
}
