<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Urlbuilder;

use InvalidArgumentException;

interface UrlbuilderFactoryInterface
{
    /**
     * @param array<int,string> $path
     * @throws InvalidArgumentException - Params can't be empty.
     * */
    public function create(
        array $path,
        string $language
    ): UrlbuilderInterface;
}
