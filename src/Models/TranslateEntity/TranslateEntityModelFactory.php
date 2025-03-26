<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\TranslateEntity;

use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelFactoryInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelInterface;

/** @deprecated */
class TranslateEntityModelFactory implements TranslateEntityModelFactoryInterface
{
    public function create(): TranslateEntityModelInterface
    {
        return new TranslateEntityModel();
    }
}
