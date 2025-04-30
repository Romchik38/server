<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Services\Translate\TranslateInterface;

/**
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class AbstractMultiLanguageAction extends AbstractDynamicRootAction
{
    public function __construct(
        protected DynamicRootInterface $dynamicRootService,
        protected TranslateInterface $translateService
    ) {
    }

    /** Use to get current language */
    protected function getLanguage(): string
    {
        return $this->dynamicRootService->getCurrentRoot()->getName();
    }

     /**
      * Use to get default language
      * */
    protected function getDefaultLanguage(): string
    {
        return $this->dynamicRootService->getDefaultRoot()->getName();
    }
}
