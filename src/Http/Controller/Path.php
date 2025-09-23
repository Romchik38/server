<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

use InvalidArgumentException;

use function count;
use function is_string;
use function preg_match;
use function sprintf;
use function urldecode;

class Path implements PathInterface
{
    /** @var array<int,Name> $parts */
    protected array $parts;

    /**
     * @param array<int,string> $parts Non encoded strings
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

    /** @param array<int,mixed|string> $parts Encoded url parts*/
    public static function fromEncodedUrlParts(array $parts): self
    {
        $decodedParts = [];
        foreach ($parts as $part) {
            if (! is_string($part)) {
                throw new InvalidArgumentException('param path part is invalid');
            }
            if (preg_match(Name::PATTERN, $part) !== 1) {
                throw new InvalidArgumentException(
                    sprintf('path name part %s is invalid', $part)
                );
            }
            $decodedParts[] = urldecode($part);
        }

        return new self($decodedParts);
    }
}
