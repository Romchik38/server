<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO\Number;

use InvalidArgumentException;
use Romchik38\Server\Domain\VO\AbstractVo;

use function sprintf;

class Number extends AbstractVo
{
    public const NAME = 'number';

    public function __construct(
        private readonly int $value
    ) {
    }

    public function __invoke(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $value): static
    {
        $oldValue = $value;
        $intId    = (int) $value;
        $strId    = (string) $intId;
        if ($oldValue !== $strId) {
            throw new InvalidArgumentException(sprintf('param %s %s is invalid', static::NAME, $value));
        }

        return new static($intId);
    }
}
