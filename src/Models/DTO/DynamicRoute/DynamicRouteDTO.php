<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DynamicRoute;

use Romchik38\Server\Api\Models\DTO\DynamicRoute\DynamicRouteDTOInterface;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

final class DynamicRouteDTO implements DynamicRouteDTOInterface
{
    /** @throws InvalidArgumentException Params can't be empty */
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
        return $this->name();
    }

    public function description(): string
    {
        return $this->description();
    }
}
