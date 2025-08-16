<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\StaticUrlbuilder;

use Romchik38\Server\Http\Utils\Urlbuilder\StaticUrlbuilderInterface;

return function (array $metaData, string $controllerResult): string {
    /** @var StaticUrlbuilderInterface $su */
    $su      = $metaData['static_urlbuilder'];
    $pageUrl = $su->withRoot('en');
    return <<<HTML
    <body><p>{$pageUrl}</p>{$controllerResult}</body>
    HTML;
};
