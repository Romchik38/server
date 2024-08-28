<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Servers;

interface ServerInterface {

    public function log(): ServerInterface;
    public function run(): ServerInterface;
}