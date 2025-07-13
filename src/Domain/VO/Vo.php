<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO;

class Vo
{
    public const NAME = 'vo';

    public function getName(): string
    {
        return static::NAME;
    }
}
