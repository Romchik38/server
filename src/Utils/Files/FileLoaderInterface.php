<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Files;

interface FileLoaderInterface
{
    /**
     * @throws FileLoaderException - On any error during loading process.
     */
    public function load(string $path): string;
}
