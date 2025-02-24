<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Root\DefaultAction as RootDefaultAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Root\DynamicAction as RootDynamicAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Catalog\DefaultAction as CatalogDefaultAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Catalog\DynamicAction as CatalogDynamicAction;
use Romchik38\Server\Tests\Unit\Services\Mappers\ControllerTree\Sitemap\DefaultAction as SitemapDefaultAction;


return function (): ControllerInterface {
    include_once(__DIR__ . '/Root/DefaultAction.php');
    include_once(__DIR__ . '/Root/DynamicAction.php');
    include_once(__DIR__ . '/Catalog/DefaultAction.php');
    include_once(__DIR__ . '/Catalog/DynamicAction.php');
    include_once(__DIR__ . '/Sitemap/DefaultAction.php');

    $root = new Controller(
        'root',
        true,
        new RootDefaultAction,
        new RootDynamicAction
    );

    $products = new Controller(
        'catalog',
        true,
        new CatalogDefaultAction,
        new CatalogDynamicAction(new ControllerTree)
    );

    $sitemap = new Controller(
        'sitemap',
        true,
        new SitemapDefaultAction(new ControllerTree)
    );

    $root->setChild($products)
        ->setChild($sitemap);

    return $root;
};
