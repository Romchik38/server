<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

use InvalidArgumentException;

use function preg_match;
use function sprintf;

class Name
{
    public const NAME    = 'Name';
    public const PATTERN = '/^[a-zA-Z0-9$\-_.+!*\'(),]+$/';

    public function __construct(
        private readonly string $name
    ) {
        if (preg_match($this::PATTERN, $name) !== 1) {
            throw new InvalidArgumentException(
                sprintf('%s %s is invalid', $this::NAME, $name)
            );
        }
    }

    public function __invoke(): string
    {
        return $this->name;
    }
}
