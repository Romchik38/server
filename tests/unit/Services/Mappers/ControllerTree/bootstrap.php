<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree;

use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Results\Controller\ControllerResultFactory;
use Romchik38\Tests\Services\Mappers\ControllerTree\Root\DefaultAction as RootDefaultAction;
use Romchik38\Tests\Services\Mappers\ControllerTree\Root\DynamicAction as RootDynamicAction;


return function () {
    include_once(__DIR__ . '/Root/DefaultAction.php');
    include_once(__DIR__ . '/Root/DynamicAction.php');

    $root = new Controller(
        'root',
        true,
        new ControllerResultFactory,
        new RootDefaultAction,
        new RootDynamicAction
    );

    $products = new Controller('products', true);
    $root->setChild($products);

    return $root;
};
