<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree;

use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Products\DefaultAction as ProductsDefaultAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Products\DynamicAction as ProductsDynamicAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Root\DefaultAction as RootDefaultAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Root\DynamicAction as RootDynamicAction;

return function (): ControllerInterface {
    include_once __DIR__ . '/Root/DefaultAction.php';
    include_once __DIR__ . '/Root/DynamicAction.php';
    include_once __DIR__ . '/Products/DefaultAction.php';
    include_once __DIR__ . '/Products/DynamicAction.php';

    $root = new Controller(
        'root',
        true,
        new RootDefaultAction(),
        new RootDynamicAction()
    );

    $products = new Controller(
        'products',
        true,
        new ProductsDefaultAction(),
        new ProductsDynamicAction()
    );
    $root->setChild($products);

    return $root;
};
