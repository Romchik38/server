<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Root;

use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;

final class DefaultAction extends Action implements DefaultActionInterface {
    protected const DATA = [
        'result' => '<h1>Welcome</h1>',
        'description' => 'Home page'
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