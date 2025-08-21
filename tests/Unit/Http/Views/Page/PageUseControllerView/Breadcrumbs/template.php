<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithoutMetadata;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

use function implode;

return function (array $metaData, DefaultViewDTOInterface $dto, string $action): string {
    $breadcrumbs      = $metaData['breadcrumbs'];
    $breadcrumbsNames = [];
    /** @var BreadcrumbDTOInterface $breadcrumb */
    foreach ($breadcrumbs as $breadcrumb) {
        $breadcrumbsNames[] = $breadcrumb->getName();
    }
    $breadcrumbsHtml = implode('/', $breadcrumbsNames);

    return <<<HTML
    <body><p>{$breadcrumbsHtml}</p><h1>{$dto->getName()}</h1><p>{$dto->getDescription()}</p></body>
    HTML;
};
