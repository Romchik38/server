<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder\Http;

use Romchik38\Server\Api\Services\Urlbuilder\UrlbuilderFactoryInterface;
use Romchik38\Server\Api\Services\Urlbuilder\UrlbuilderInterface;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

class UrlbuilderFactory implements UrlbuilderFactoryInterface
{
    public function __construct(
        protected readonly string $delimiter = '/'
    ) {}
    
    public function create(array $path, string $language): UrlbuilderInterface
    {

        if (count($path) === 0) {
            throw new InvalidArgumentException('param path is empty');
        }

        if (strlen($language) === 0) {
            throw new InvalidArgumentException('param language is empty');
        }

        return new Urlbuilder(
            $path,
            $language,
            $this->delimiter
        );
    }
}
