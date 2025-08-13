<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use InvalidArgumentException;

interface StaticUrlbuilderInterface
{
    /**
     * @throws InvalidArgumentException
     * @param array<string,string> $params - Key/value for query string
     * */
    public function withRoot(
        string $rootName,
        array $params = [],
        string $fragment = ''
    ): string;
}
