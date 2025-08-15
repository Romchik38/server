<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

interface SingleViewInterface extends ViewInterface
{
    public function setHandlerData(DefaultViewDTOInterface $data): self;
}
