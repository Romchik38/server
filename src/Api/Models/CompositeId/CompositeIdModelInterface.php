<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\CompositeId;

use Romchik38\Server\Api\Models\ModelInterface;

/** 
 * Used to create a model with composite id.
 * Composite Id - is a primary key with more than 1 field 
 */
interface CompositeIdModelInterface extends ModelInterface
{    
    public function getId(): CompositeIdDTOInterface;
    public function setId(CompositeIdDTOInterface $id): CompositeIdModelInterface;
}
