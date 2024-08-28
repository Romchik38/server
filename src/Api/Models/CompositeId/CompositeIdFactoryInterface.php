<?php

namespace Romchik38\Server\Api\Models\CompositeId;

interface CompositeIdFactoryInterface
{
    public function create(): CompositeIdModelInterface;
}
