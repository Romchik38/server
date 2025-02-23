<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Actions;

use Psr\Http\Message\ResponseInterface;

interface DefaultActionInterface extends ActionInterface
{
    /**
     * The last part of the chain.
     * Returns the result to client
     *
     * @return ResponseInterface Action responses
     */
    public function execute(): ResponseInterface;

    /**
     * Returns action description.
     * Description used in the mapper services to represents the Action
     */
    public function getDescription(): string;
}
