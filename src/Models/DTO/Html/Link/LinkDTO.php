<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Html\Link;

use Romchik38\Server\Api\Models\DTO\Html\Link\LinkDTOInterface;
use Romchik38\Server\Models\DTO;

/**
 * LinkDTOFactoryInterface is responsible to create the entity
 */
class LinkDTO extends DTO implements LinkDTOInterface
{
    public function __construct(
        string $name,
        string $description,
        string $url,
    ) {
        $this->data[LinkDTOInterface::NAME_FIELD] = $name;
        $this->data[LinkDTOInterface::DESCRIPTION_FIELD] = $description;
        $this->data[LinkDTOInterface::URL_FIELD] = $url;
    }
    public function getName(): string
    {
        return $this->data[LinkDTOInterface::NAME_FIELD];
    }
    public function getDescription(): string
    {
        return $this->data[LinkDTOInterface::DESCRIPTION_FIELD];
    }
    public function getUrl(): string
    {
        return $this->data[LinkDTOInterface::URL_FIELD];
    }
}
