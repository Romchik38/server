<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers;

use Psr\Http\Message\ResponseInterface;

interface ControllerResultInterface
{
    const RESPONSE_FIELD = 'response';
    const PATH_FIELD     = 'path';
    const TYPE_FIELD     = 'type';
    /**
     * returns result from the action
     *
     * @return ResponseInterface Action response
     */
    public function getResponse(): ResponseInterface;

    /**
     * returns the full path to action
     * direction from root to action
     *
     * @return array<int,string> - ['root', 'controller_name', ... 'action_name']
     */
    public function getPath(): array;

    /**
     * type of the controller action
     *
     * @return string [a const from ActionInterface]
     */
    public function getType(): string;
}
