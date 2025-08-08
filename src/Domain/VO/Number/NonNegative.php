<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO\Number;

use InvalidArgumentException;

use function sprintf;

class NonNegative extends Number
{
    public const NAME = 'non-negative-number';

    /** @throws InvalidArgumentException */
    public function __construct(
        int $value
    ) {
        if ($value < 0) {
            throw new InvalidArgumentException(sprintf('param %s must be greater or equal than 0', static::NAME));
        }
        parent::__construct($value);
    }
}
