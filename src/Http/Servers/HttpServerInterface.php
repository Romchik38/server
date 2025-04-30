<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Servers;

interface HttpServerInterface extends ServerInterface
{
    public const DEFAULT_SERVER_ERROR_CODE    = 500;
    public const DEFAULT_SERVER_ERROR_MESSAGE = 'Server 500 error. Please try later';
}
