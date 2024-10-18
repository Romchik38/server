<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Html\Breadcrumb;

use Romchik38\Server\Api\Models\DTO\Html\Breadcrumb\BreadcrumbDTOInterface;
use Romchik38\Server\Models\DTO\Html\Link\LinkDTO;

class BreadcrumbDTO extends LinkDTO implements BreadcrumbDTOInterface
{
    public function __construct(
        string $name,
        string $description,
        string $url,
        BreadcrumbDTOInterface|null $prev
    ) {
        parent::__construct($name, $description, $url);
        $this->data[BreadcrumbDTOInterface::PREV_FIELD] = $prev;
    }

    public function getPrev(): BreadcrumbDTOInterface|null
    {
        return $this->data[BreadcrumbDTOInterface::PREV_FIELD];
    }

}
