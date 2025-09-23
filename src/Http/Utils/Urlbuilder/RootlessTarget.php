<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Controller\PathInterface;

use function implode;
use function urlencode;

class RootlessTarget implements TargetInterface
{
    public function fromPath(PathInterface $path): string
    {
        $encodedParts = [];
        foreach ($path() as $part) {
            $encodedParts[] = urlencode($part);
        }
        return '/' . implode('/', $encodedParts);
    }
}
