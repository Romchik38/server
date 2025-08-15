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
        protected readonly Closure $generateTemplate,
        protected readonly Closure $handlerTemplate,
        ?MetaDataInterface $metaDataService = null
    ) {
        parent::__construct($metaDataService);
    }

    public function toString(): string
    {
        if ($this->handlerData === null) {
            throw new ViewBuildException('View build aborted - handlerData was not set');
        }

        /** 1. create metadata for header, etc */
        $this->prepareMetaData();

        /**
         * 2. generate html from handler template
         */
        $handlerResult = call_user_func(
            $this->handlerTemplate,
            $this->metaData,
            $this->handlerData
        );

        /** 3. generate html document */
        return call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $handlerResult
        );
    }
}
