<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Request\Http;

interface ServerRequestBodyParserInterface
{
    /** 
     * @return array|null array with data on success, null on fail
     */
    public function parseBody(string $body): array|null;
}
