<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Http\Utils\DynamicRoot\DynamicRootInterface;

/**
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class AbstractDynamicRootAction extends AbstractAction implements ActionInterface
{
    public function __construct(
        protected DynamicRootInterface $dynamicRootService
    ) {
    }
}
