<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

abstract class AbstractSingleView extends AbstractView implements SingleViewInterface
{
    protected ?DefaultViewDTOInterface $handlerData = null;

    public function setHandlerData(DefaultViewDTOInterface $data): self
    {
        $this->handlerData = $data;
        return $this;
    }
}
