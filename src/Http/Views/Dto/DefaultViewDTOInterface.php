<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto;

interface DefaultViewDTOInterface
{
    public const DEFAULT_NAME_FIELD        = 'default_name';
    public const DEFAULT_DESCRIPTION_FIELD = 'default_description';

    public function getDescription(): string;

    public function getName(): string;
}
