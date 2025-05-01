<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto;

interface DefaultViewDTOFactoryInterface
{
    public function create(
        string $name,
        string $description
    ): DefaultViewDTOInterface;
}
