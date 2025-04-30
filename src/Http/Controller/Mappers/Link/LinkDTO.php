<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\Link;

/**
 * LinkDTOFactoryInterface is responsible to create the entity
 */
class LinkDTO implements LinkDTOInterface
{
    public function __construct(
        protected readonly string $name,
        protected readonly string $description,
        protected readonly string $url,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
