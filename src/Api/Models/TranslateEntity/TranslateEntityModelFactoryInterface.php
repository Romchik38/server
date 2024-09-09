<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\TranslateEntity;

use Romchik38\Server\Api\Models\ModelFactoryInterface;

interface TranslateEntityModelFactoryInterface extends ModelFactoryInterface
{
    /**
     * Create a translate entity with empty fields
     * 
     * @return TranslateEntityModelInterface an empty translate entity
     */
    public function create(): TranslateEntityModelInterface;
}
