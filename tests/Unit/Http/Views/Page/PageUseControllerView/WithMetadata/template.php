<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithoutMetadata;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

use function is_string;

return function (array $metaData, DefaultViewDTOInterface $dto, string $action): string {
    $user = $metaData['user'] ?? null;
    if (! is_string($user) || $user === '') {
        $userHtml = '';
    } else {
        $userHtml = <<<USER
        <p>Hello {$user}</p>
        USER;
    }

    return <<<HTML
    <body>{$userHtml}<h1>{$dto->getName()}</h1><p>{$dto->getDescription()}</p></body>
    HTML;
};
