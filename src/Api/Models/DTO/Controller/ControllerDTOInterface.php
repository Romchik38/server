<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Controller;

use JsonSerializable;
use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface ControllerDTOInterface extends DTOInterface, JsonSerializable
{
    /**
     * Used in the jsonSerialize() method
     */
    const NAME_FIELD        = 'name';
    const PATH_FIELD        = 'path';
    const CHILDREN_FIELD    = 'children';
    const DESCRIPTION_FILED = 'description';

    /**
     * Returns route name
     */
    public function getName(): string;

    /**
     * Path to this controller
     *
     * @return string[] Parrents names where 1st element is the root name
     */
    public function getPath(): array;

    /**
     * @return ControllerDTOInterface[]
     */
    public function getChildren(): array;

    /** Route description */
    public function getDescription(): string;

    /** json representation */
    public function jsonSerialize(): mixed;
}
