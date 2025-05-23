<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Dto;

final class ControllerDTO implements ControllerDTOInterface
{
    /**
     * @param array<int,ControllerDTOInterface> $children
     * @param array<int,string> $path
     */
    public function __construct(
        private readonly string $name,
        private array $path,
        private array $children,
        private readonly string $description
    ) {
    }

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
            ControllerDTOInterface::NAME_FIELD        => $this->name,
            ControllerDTOInterface::PATH_FIELD        => $this->path,
            ControllerDTOInterface::CHILDREN_FIELD    => $this->children,
            ControllerDTOInterface::DESCRIPTION_FILED => $this->description,
        ];
    }
}
