<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Dto;

use InvalidArgumentException;

use function strlen;

final class DynamicRouteDTO implements DynamicRouteDTOInterface
{
    /** @throws InvalidArgumentException - Params can't be empty. */
    public function __construct(
        protected readonly string $name,
        protected readonly string $description,
    ) {
        if (strlen($name) === 0) {
            throw new InvalidArgumentException('param name is empty');
        }
        if (strlen($description) === 0) {
            throw new InvalidArgumentException('param description is empty');
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }
}
