<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto;

/** @deprecated */
class DefaultViewDTOFactory implements DefaultViewDTOFactoryInterface
{
    public function create(string $name, string $description): DefaultViewDTOInterface
    {
        return new DefaultViewDTO($name, $description);
    }
}
