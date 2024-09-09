<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Actions;

use Romchik38\Server\Api\Services\DymanicRoot\DymanicRootInterface;
use Romchik38\Server\Api\Services\Translate\TranslateInterface;

/** 
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class MultiLanguageAction extends DynamicRootAction
{
    public function __construct(
        protected readonly DymanicRootInterface $dymanicRootService,
        protected readonly TranslateInterface $translateService
    ) {
    }
}
