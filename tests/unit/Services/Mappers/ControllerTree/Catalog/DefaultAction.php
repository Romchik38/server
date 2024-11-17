<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Catalog;

use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;

final class DefaultAction extends Action implements DefaultActionInterface {
    protected const DATA = [
        'result' => '<h1>More than 20 000 products with great discount 50% for 1 day</h1>',
        'description' => 'Products catalog'
    ];

    public function execute(): string
    {
        return $this::DATA['result'];
    }

    public function getDescription(): string
    {
        return $this::DATA['description'];
    }
}