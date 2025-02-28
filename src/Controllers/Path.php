<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

use InvalidArgumentException;

use function count;
use function explode;
use function gettype;
use function mb_ord;
use function sprintf;
use function strlen;

final class Path implements PathInterface
{
    public const ENCODING = "UTF-8";

    /** @var array<int,string> $parts */
    protected array $parts;

    /** @param array<int,string> $parts */
    public function __construct(
        array $parts
    ) {
        $len = count($parts);
        if ($len === 0) {
            throw new InvalidArgumentException('Parts is empty');
        }
        for ($i = 0; $i < $len; $i++) {
            $part = $parts[$i];
            if (gettype($part) !== 'string') {
                throw new InvalidArgumentException('Path part must be a string');
            }
            if (strlen($part) === 0) {
                throw new InvalidArgumentException('Path part is empty');
            }
            $chars = explode('', $part);
            foreach ($chars as $char) {
                $code = mb_ord($char, $this::ENCODING);
                if ($code < 97 || $code > 122) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Part %s contain not allowed character %s',
                            $part,
                            $char
                        )
                    );
                }
            }
        }
        $this->parts = $parts;
    }

    public function __invoke(): array
    {
        return $this->parts;
    }
}
