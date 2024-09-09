<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Actions;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Services\DymanicRoot\DymanicRootInterface;

/** 
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class DynamicRootAction extends Action implements ActionInterface
{
    public  function __construct(
        protected readonly DymanicRootInterface $dymanicRootService
    ) {}
}
