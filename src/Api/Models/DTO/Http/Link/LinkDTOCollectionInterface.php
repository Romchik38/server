<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Http\Link;

interface LinkDTOCollectionInterface
{
    /**
     * Return all links if $paths was not provided 
     * @param array<int,array<int,string>> $paths like [['root'], ['root', 'about']]
     * @return LinkDTOInterface[]
     */
    public function getLinksByPaths(array $paths = []): array;
}
