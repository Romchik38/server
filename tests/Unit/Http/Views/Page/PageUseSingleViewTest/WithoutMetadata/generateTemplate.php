<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseSingleViewTest\WithoutMetadata;

return function (array $metaData, string $handlerResult): string {
    return <<<HTML
    <body>{$handlerResult}</body>
    HTML;
};
