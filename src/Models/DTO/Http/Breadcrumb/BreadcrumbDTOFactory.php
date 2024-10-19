<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Http\Breadcrumb;

use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOInterface;

class BreadcrumbDTOFactory implements BreadcrumbDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $url,
        BreadcrumbDTOInterface|null $prev
    ): BreadcrumbDTOInterface {
        return new BreadcrumbDTO(
            $name,
            $description,
            $url,
            $prev
        );
    }
}
