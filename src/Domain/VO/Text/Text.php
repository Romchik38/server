<?php

declare(strict_types=1);

namespace Romchik38\Server\Domain\VO\Text;

use Romchik38\Server\Domain\VO\AbstractVo;

class Text extends AbstractVo
{
    public const NAME = 'text';

    public function __construct(
        private readonly string $value
    ) {
    }

    public function __invoke(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
