<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page\WithMetadata;

use Romchik38\Server\Http\Views\AbstractMetaData;

final class MetaDataService extends AbstractMetaData
{
    public function __construct(
        string $userName
    ) {
        $this->hash['user'] = $userName;
    }
}
