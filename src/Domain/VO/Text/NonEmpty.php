<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO\Text;

use InvalidArgumentException;

use function sprintf;

class NonEmpty extends Text
{
    public const NAME = 'non empty text';

    /** @throws InvalidArgumentException */
    public function __construct(
        string $value
    ) {
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('param %s is empty', static::NAME));
        }
        parent::__construct($value);
    }
}
