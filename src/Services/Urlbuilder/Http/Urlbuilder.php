<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder\Http;

use Romchik38\Server\Api\Services\Urlbuilder\UrlbuilderInterface;

class Urlbuilder implements UrlbuilderInterface
{
    protected readonly string $prefix;

    /** 
     * @param array<int,string> $path 
     * */
    public function __construct(
        protected readonly array $path,
        protected readonly string $language,
        protected readonly string $delimiter
    ) {
        $url = [...$path];
        $url[0] = $language;
        $this->prefix = sprintf(
            '%s%s',
            $this->delimiter,
            implode($this->delimiter, $url)
        );
    }

    public function prefix(): string
    {
        return $this->prefix;
    }

    public function add(string $part, string $delimiter = ''): string
    {
        return sprintf('%s%s%s', $this->prefix, $delimiter, $part);
    }

    public function addWithDelimiter(string $part): string
    {
        return $this->add($part, $this->delimiter);
    }
}
