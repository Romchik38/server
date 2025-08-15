<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\WithoutMetadata;

use function is_string;

return function (array $metaData, string $controllerResult): string {
    $user = $metaData['user'] ?? null;
    if (! is_string($user) || $user === '') {
        $userHtml = '';
    } else {
        $userHtml = <<<USER
        <p>Hello {$user}</p>
        USER;
    }

    return <<<HTML
    <body>{$userHtml}{$controllerResult}</body>
    HTML;
};
