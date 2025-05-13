<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Http\Controller\Actions\ActionInterface;

interface DefaultActionInterface extends ActionInterface
{
    /**
     * Returns action description.
     * Description used in the mapper services to represents the Action
     */
    public function getDescription(): string;
}
