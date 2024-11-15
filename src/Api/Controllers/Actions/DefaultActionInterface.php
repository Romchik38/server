<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Actions;

interface DefaultActionInterface extends ActionInterface
{

    /** 
     * The last part of the chain.
     * Returns the result to client
     * 
     * @return string [result]
     */
    public function execute(): string;

    /** 
     * Returns action description. 
     * Description used in the mapper services to represent action
     */
    public function getDescription(): string;
}
