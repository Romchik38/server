<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\StaticUrlbuilder;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

return function (array $metaData, DefaultViewDTOInterface $dto, string $action): string {
    return <<<HTML
    <h1>{$dto->getName()}</h1><p>{$dto->getDescription()}</p>
    HTML;
};
