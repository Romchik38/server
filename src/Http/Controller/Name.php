<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

use InvalidArgumentException;
use Romchik38\Server\Domain\VO\Text\NonEmpty;

use function preg_match;
use function sprintf;
use function urldecode;

class Name extends NonEmpty
{
    public const NAME    = 'controller name';
    public const PATTERN = '/^[a-zA-Z0-9$\-_.+!*\'(),%]+$/';

    public static function fromEncodedUrlPart(string $part): self
    {
        if (preg_match(self::PATTERN, $part) !== 1) {
            throw new InvalidArgumentException(
                sprintf('name part %s is invalid', $part)
            );
        }

        return new self(urldecode($part));
    }
}
