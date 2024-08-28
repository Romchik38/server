<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\CompositeId;

use Romchik38\Server\Api\Models\CompositeId\CompositeIdFactoryInterface;
use Romchik38\Server\Api\Models\CompositeId\CompositeIdModelInterface;
use Romchik38\Server\Models\CompositeId\CompositeIdModel;

class CompositeIdFactory implements CompositeIdFactoryInterface
{
    public function create(): CompositeIdModelInterface
    {
        return new CompositeIdModel();
    }
}
