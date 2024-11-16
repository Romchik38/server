<?php

declare(strict_types=1);

use Romchik38\Server\Controllers\Controller;

$root = new Controller('root', true);
$products = new Controller('products', true);
$root->setChild($products);

return $products;