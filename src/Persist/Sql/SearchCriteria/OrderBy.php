<?php

declare(strict_types=1);

namespace Romchik38\Server\Persist\Sql\SearchCriteria;

use InvalidArgumentException;

use function strlen;
use function strtoupper;

class OrderBy implements OrderByInterface
{
    /**
     * @param string $field Field to order by, must not be empty.
     * @throws InvalidArgumentException
     * */
    public function __construct(
        protected string $field,
        protected string $direction = self::ASC_DIRECTION,
        protected string $nulls = self::NULLS_LAST_OPTION
    ) {
        if (strlen($field) === 0) {
            throw new InvalidArgumentException('param field must not be empty');
        }

        if (
            strtoupper($direction) !== self::ASC_DIRECTION &&
            strtoupper($direction) !== self::DESC_DIRECTION
        ) {
            throw new InvalidArgumentException('param direction is invalid');
        }

        if (
            strtoupper($nulls) !== self::NULLS_FIRST_OPTION &&
            strtoupper($nulls) !== self::NULLS_LAST_OPTION
        ) {
            throw new InvalidArgumentException('param nulls is invalid');
        }
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getNulls(): string
    {
        return $this->nulls;
    }
}
