<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO;

use Stringable;

abstract class AbstractVo implements Stringable
{
    public const NAME = 'vo';

    public function getName(): string
    {
        return static::NAME;
    }

    abstract public function __toString(): string;
}
