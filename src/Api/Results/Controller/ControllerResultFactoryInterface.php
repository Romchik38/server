<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Results\Controller;

interface ControllerResultFactoryInterface
{
    /**
     * return new instance of ControllerResultInterface
     * 
     * @param string $response [response from action]
     * @param array $path [full path from root to action]
     * @param string $type [a const from ActionInterface]
     * @return ControllerResultInterface
     */
    public function create(string $response, array $path, string $type): ControllerResultInterface;
}
