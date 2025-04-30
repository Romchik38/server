<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree;

use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Products\DefaultAction as ProductsDefaultAction;
use Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Products\DynamicAction as ProductsDynamicAction;
use Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Root\DefaultAction as RootDefaultAction;
use Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Root\DynamicAction as RootDynamicAction;
use Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Sitemap\DefaultAction as SitemapDefaultAction;

return function (): ControllerInterface {
    include_once __DIR__ . '/Root/DefaultAction.php';
    include_once __DIR__ . '/Root/DynamicAction.php';
    include_once __DIR__ . '/Products/DefaultAction.php';
    include_once __DIR__ . '/Products/DynamicAction.php';
    include_once __DIR__ . '/Sitemap/DefaultAction.php';

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

    $sitemap = new Controller(
        'sitemap',
        true,
        new SitemapDefaultAction(new ControllerTree())
    );

    $root->setChild($products)
        ->setChild($sitemap);

    return $root;
};
