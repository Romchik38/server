<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DefaultView;

use Romchik38\Server\Models\DTO;
use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

class DefaultViewDTO extends DTO implements DefaultViewDTOInterface
{
    public function __construct(string $name, string $description)
    {
        $this->data[DefaultViewDTOInterface::DEFAULT_DESCRIPTION_FIELD] = $description;
        $this->data[DefaultViewDTOInterface::DEFAULT_NAME_FIELD] = $name;
    }

    public function getDescription(): string
    {
        return $this->getData(DefaultViewDTOInterface::DEFAULT_DESCRIPTION_FIELD);
    }

    public function getName(): string
    {
        return $this->getData(DefaultViewDTOInterface::DEFAULT_NAME_FIELD);
    }
}
