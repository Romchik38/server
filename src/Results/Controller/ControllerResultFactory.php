<?php

declare(strict_types=1);

namespace Romchik38\Server\Results\Controller;

use Romchik38\Server\Api\Results\Controller\ControllerResultFactoryInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultInterface;

class ControllerResultFactory implements ControllerResultFactoryInterface
{
    public function create(string $response, array $path, string $type): ControllerResultInterface
    {
        return new ControllerResult($response, $path, $type);
    }
}
