<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Servers;

use Psr\Http\Message\ServerRequestInterface;

interface HttpServerInterface
{
    public const DEFAULT_SERVER_ERROR_CODE    = 500;
    public const DEFAULT_SERVER_ERROR_MESSAGE = 'Server 500 error. Please try later';
    public const REQUEST_ERROR_ATTRIBUTE_NAME = 'server_error';

    public function handle(ServerRequestInterface $request): void;
}
