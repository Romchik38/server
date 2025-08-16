<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithoutMetadata;

use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbDTOInterface;

use function implode;

return function (array $metaData, string $controllerResult): string {
    $breadcrumbs = $metaData['breadcrumbs'];
    /** @var BreadcrumbDTOInterface $breadcrumb */
    $breadcrumbsNames = [];
    foreach ($breadcrumbs as $breadcrumb) {
        $breadcrumbsNames[] = $breadcrumb->getName();
    }
    $breadcrumbsHtml = implode('/', $breadcrumbsNames);
    return <<<HTML
    <body><p>{$breadcrumbsHtml}</p>{$controllerResult}</body>
    HTML;
};
