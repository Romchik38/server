<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Actions;

use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Translate\TranslateInterface;

/** 
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class MultiLanguageAction extends DynamicRootAction
{
    public function __construct(
        protected readonly DynamicRootInterface $DynamicRootService,
        protected readonly TranslateInterface $translateService
    ) {}

    /** Use to get current language */
    protected function getLanguage(): string
    {
        return $this->DynamicRootService->getCurrentRoot()->getName();
    }
}
