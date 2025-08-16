<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\StaticUrlbuilder;

use Romchik38\Server\Http\Views\AbstractMetaData;

final class MetaDataService extends AbstractMetaData
{
    public function __construct(
        string $language
    ) {
        $this->hash['language'] = $language;
    }
}
