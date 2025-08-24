<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

use InvalidArgumentException;

use function count;

class Path implements PathInterface
{
    /** @var array<int,Name> $parts */
    protected array $parts;

    /**
     * @param array<int,string> $parts
     * @throws InvalidArgumentException
     * */
    public function __construct(
        array $parts
    ) {
        $len = count($parts);
        if ($len === 0) {
            throw new InvalidArgumentException('Parts is empty');
        }
        $this->parts = [];
        foreach ($parts as $part) {
            $this->parts[] = new Name($part);
        }
    }

    public function __invoke(): array
    {
        $parts = [];
        foreach ($this->parts as $part) {
            $parts[] = $part();
        }
        return $parts;
    }
}
