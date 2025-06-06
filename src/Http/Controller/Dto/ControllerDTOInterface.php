<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Dto;

use JsonSerializable;

interface ControllerDTOInterface extends JsonSerializable
{
    /**
     * Used in the jsonSerialize() method
     */
    public const NAME_FIELD        = 'name';
    public const PATH_FIELD        = 'path';
    public const CHILDREN_FIELD    = 'children';
    public const DESCRIPTION_FILED = 'description';

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
