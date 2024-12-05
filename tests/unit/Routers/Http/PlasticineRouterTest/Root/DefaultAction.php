<?php

declare(strict_types=1);

use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;

final class DefaultAction extends Action implements DefaultActionInterface
{
    public function execute(): string
    {
        return 'hello world';
    }

    public function getDescription(): string
    {
        return 'Home Page';
    }
}
