<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\Breadcrumb;

use Romchik38\Server\Http\Controller\Mappers\Link\LinkDTO;

class BreadcrumbDTO extends LinkDTO implements BreadcrumbDTOInterface
{
    public function __construct(
        string $name,
        string $description,
        string $url,
        protected readonly BreadcrumbDTOInterface|null $prev
    ) {
        parent::__construct($name, $description, $url);
    }

    public function getPrev(): BreadcrumbDTOInterface|null
    {
        return $this->prev;
    }
}
