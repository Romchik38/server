<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

interface ControllerViewInterface extends ViewInterface
{
    public function setController(
        ControllerInterface $controller,
        string $action = ''
    ): self;

    public function setControllerData(DefaultViewDTOInterface $data): self;
}
