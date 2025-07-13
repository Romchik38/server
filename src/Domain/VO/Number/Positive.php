<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO\Number;

use InvalidArgumentException;

use function sprintf;

class Positive extends Number
{
    public const NAME = 'positive-number';

    /** @throws InvalidArgumentException */
    public function __construct(
        int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException(sprintf('param %s must be greater than 0', static::NAME));
        }
        parent::__construct($value);
    }
}
