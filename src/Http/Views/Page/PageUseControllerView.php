<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Page;

use Closure;
use Romchik38\Server\Http\Views\AbstractControllerView;
use Romchik38\Server\Http\Views\MetaDataInterface;

use function call_user_func;

class PageUseControllerView extends AbstractControllerView
{
    public function __construct(
        protected readonly Closure $generateTemplate,
        protected readonly Closure $controllerTemplate,
        ?MetaDataInterface $metaDataService = null
    ) {
        parent::__construct($metaDataService);
    }

    public function toString(): string
    {
        /** 1. create metadata for header, etc */
        $this->prepareMetaData();

        /**
         * 2. generate html from controller template
         */
        $controllerResult = call_user_func(
            $this->controllerTemplate,
            $this->metaData,
            $this->controllerData
        );

        /** 3. generate html document */
        return call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $controllerResult
        );
    }
}
