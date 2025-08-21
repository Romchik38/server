<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest;

use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;

abstract class AbstractRequestMiddleware implements RequestMiddlewareInterface
{
    public function getAttributeName(): string
    {
        return 'some_name';
    }
}
