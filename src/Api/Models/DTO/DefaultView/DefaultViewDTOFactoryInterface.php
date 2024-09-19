<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\DefaultView;

interface DefaultViewDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $content = ''
    ): DefaultViewDTOInterface;
}
