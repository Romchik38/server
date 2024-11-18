<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Results\Controller\ControllerResultFactory;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;
use Romchik38\Tests\Services\Mappers\ControllerTree\Root\DefaultAction as RootDefaultAction;
use Romchik38\Tests\Services\Mappers\ControllerTree\Root\DynamicAction as RootDynamicAction;
use Romchik38\Tests\Services\Mappers\ControllerTree\Products\DefaultAction as ProductsDefaultAction;
use Romchik38\Tests\Services\Mappers\ControllerTree\Products\DynamicAction as ProductsDynamicAction;
use Romchik38\Tests\Services\Mappers\ControllerTree\Sitemap\DefaultAction as SitemapDefaultAction;


return function (): ControllerInterface {
    include_once(__DIR__ . '/Root/DefaultAction.php');
    include_once(__DIR__ . '/Root/DynamicAction.php');
    include_once(__DIR__ . '/Products/DefaultAction.php');
    include_once(__DIR__ . '/Products/DynamicAction.php');
    include_once(__DIR__ . '/Sitemap/DefaultAction.php');

    $root = new Controller(
        'root',
        true,
        new ControllerResultFactory,
        new RootDefaultAction,
        new RootDynamicAction
    );

    $products = new Controller(
        'products', 
        true, 
        new ControllerResultFactory, 
        new ProductsDefaultAction,
        new ProductsDynamicAction
    );

    $sitemap = new Controller(
        'sitemap', 
        true, 
        new ControllerResultFactory, 
        new SitemapDefaultAction(new ControllerTree)
    );

    $root->setChild($products)
        ->setChild($sitemap);

    return $root;
};