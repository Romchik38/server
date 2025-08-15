<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\WithoutMetadata;

return function (array $metaData, string $controllerResult): string {
    return <<<HTML
    <body>{$controllerResult}</body>
    HTML;
};
