<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DefaultView;

use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

class DefaultViewDTOFactory implements DefaultViewDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $content = ''
    ): DefaultViewDTOInterface {
        return new DefaultViewDTO($name, $description, $content);
    }
}
