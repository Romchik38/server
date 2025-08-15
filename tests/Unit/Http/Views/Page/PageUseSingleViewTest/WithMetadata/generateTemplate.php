<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseSingleViewTest\WithMetadata;

use function is_string;

return function (array $metaData, string $handlerResult): string {
    $user = $metaData['user'] ?? null;
    if (! is_string($user) || $user === '') {
        $userHtml = '';
    } else {
        $userHtml = <<<USER
        <p>Hello {$user}</p>
        USER;
    }

    return <<<HTML
    <body>{$userHtml}{$handlerResult}</body>
    HTML;
};
