<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Controller;

use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Models\DTO;

final class ControllerDTO extends DTO implements ControllerDTOInterface
{
    /** 
     * @param array<int,ControllerDTO> $children
     */
    public function __construct(
        protected readonly string $name,
        protected array $path,
        protected array $children,
        protected readonly string $description
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize(): mixed
    {
        return [
            ControllerDTOInterface::NAME_FIELD => $this->name,
            ControllerDTOInterface::PATH_FIELD => $this->path,
            ControllerDTOInterface::CHILDREN_FIELD => $this->children,
            ControllerDTOInterface::DESCRIPTION_FILED => $this->description,
        ];
    }
}
