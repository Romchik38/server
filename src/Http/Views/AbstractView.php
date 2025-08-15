<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use MetadataInterface;

abstract class AbstractView implements ViewInterface
{
    /** @var array<string,mixed> $metaData */
    protected array $metaData = [];

    public function __construct(
        private readonly ?MetaDataInterface $metaDataService = null
    ) {
    }

    protected function prepareMetaData(): void
    {
        if ($this->metaDataService === null) {
            return;
        }

        $metaData = $this->metaDataService->getAllData();
        foreach ($metaData as $key => $value) {
            $this->metaData[$key] = $value;
        }
    }
}
