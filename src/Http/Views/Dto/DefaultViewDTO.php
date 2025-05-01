<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto;

class DefaultViewDTO implements DefaultViewDTOInterface
{
    public function __construct(
        protected readonly string $name,
        protected readonly string $description
    ) {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            self::DEFAULT_NAME_FIELD        => $this->name,
            self::DEFAULT_DESCRIPTION_FIELD => $this->description,
        ];
    }
}
