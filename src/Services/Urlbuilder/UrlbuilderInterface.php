<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder;

use Romchik38\Server\Controllers\PathInterface;

interface UrlbuilderInterface
{
    /**
     * @param array<string,string> $params - Key/value for query string
     * */
    public function fromPath(
        PathInterface $path,
        array $params = [],
        string $fragment = ''
    ): string;
}
