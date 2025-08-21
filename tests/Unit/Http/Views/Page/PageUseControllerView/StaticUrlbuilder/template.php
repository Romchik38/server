<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithoutMetadata;

use Romchik38\Server\Http\Utils\Urlbuilder\StaticUrlbuilderInterface;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

return function (array $metaData, DefaultViewDTOInterface $dto, string $action): string {
    /** @var StaticUrlbuilderInterface $su */
    $su      = $metaData['static_urlbuilder'];
    $pageUrl = $su->withRoot('en');

    return <<<HTML
    <body><p>{$pageUrl}</p><h1>{$dto->getName()}</h1><p>{$dto->getDescription()}</p></body>
    HTML;
};
