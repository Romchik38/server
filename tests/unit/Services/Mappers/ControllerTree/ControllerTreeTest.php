<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

class ControllerTreeTest extends TestCase {

    public function setUp(): void {

    }

    public function testGetRootControllerDTO(): void{
        $controllerTree = new ControllerTree();
        $products = include_once(__DIR__ . '/bootstrap.php');
        $controllerDTO = $controllerTree->getRootControllerDTO($root);

    }    
}