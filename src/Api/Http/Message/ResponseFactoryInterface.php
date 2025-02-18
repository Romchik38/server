<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Http\Message;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function create(): ResponseInterface;
}