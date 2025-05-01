<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto;

use JsonSerializable;

interface DefaultViewDTOInterface extends JsonSerializable
{
    public const DEFAULT_NAME_FIELD        = 'default_name';
    public const DEFAULT_DESCRIPTION_FIELD = 'default_description';

    public function getDescription(): string;

    public function getName(): string;
}
