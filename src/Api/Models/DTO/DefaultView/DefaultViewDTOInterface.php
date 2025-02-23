<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\DefaultView;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface DefaultViewDTOInterface extends DTOInterface
{
    const DEFAULT_NAME_FIELD        = 'default_name';
    const DEFAULT_DESCRIPTION_FIELD = 'default_description';

    public function getDescription(): string;

    public function getName(): string;
}
