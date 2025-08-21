<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Page;

use Closure;
use Romchik38\Server\Http\Views\AbstractSingleView;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;
use Romchik38\Server\Http\Views\MetaDataInterface;

use function call_user_func;

class PageUseSingleView extends AbstractSingleView
{
    public function __construct(
        protected readonly Closure $template,
        ?MetaDataInterface $metaDataService = null
    ) {
        parent::__construct($metaDataService);
    }

    public function toString(): string
    {
        if ($this->handlerData === null) {
            throw new ViewBuildException('View build aborted - handlerData was not set');
        }

        $this->prepareMetaData();

        return call_user_func(
            $this->template,
            $this->metaData,
            $this->handlerData
        );
    }
}
