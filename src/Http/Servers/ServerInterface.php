<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Servers;

/** @deprecated */
interface ServerInterface
{
    public const SERVER_ERROR_CONTROLLER_NAME = 'server-error';

    public function run(): ServerInterface;
}
