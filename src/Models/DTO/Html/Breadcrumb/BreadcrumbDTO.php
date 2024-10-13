<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Html\Breadcrumb;

use Romchik38\Server\Models\DTO;
use Romchik38\Server\Api\Models\DTO\Html\Breadcrumb\BreadcrumbDTOInterface;

class BreadcrumbDTO extends DTO implements BreadcrumbDTOInterface
{

    public function __construct(
        string $name,
        string $description,
        string $url,
        BreadcrumbDTOInterface|null $prev
    ) {
        $this->data[BreadcrumbDTOInterface::NAME_FIELD] = $name;
        $this->data[BreadcrumbDTOInterface::DESCRIPTION_FIELD] = $description;
        $this->data[BreadcrumbDTOInterface::URL_FIELD] = $url;
        $this->data[BreadcrumbDTOInterface::PREV_FIELD] = $prev;
    }

    public function getPrev(): BreadcrumbDTOInterface|null
    {
        return $this->data[BreadcrumbDTOInterface::PREV_FIELD];
    }

    public function getDescription(): string
    {
        return $this->data[BreadcrumbDTOInterface::DESCRIPTION_FIELD];
    }

    public function getName(): string
    {
        return $this->data[BreadcrumbDTOInterface::NAME_FIELD];
    }

    public function getUrl(): string
    {
        return $this->data[BreadcrumbDTOInterface::URL_FIELD];
    }
}
