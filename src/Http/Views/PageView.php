<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Closure;

use function call_user_func;

class PageView extends AbstractView implements HttpViewInterface
{
    /** @var array<string,mixed> $metaData */
    protected array $metaData = [];

    public function __construct(
        protected Closure $generateTemplate,
        protected Closure $controllerTemplate
    ) {
    }

    protected function setMetadata(string $key, mixed $value): PageView
    {
        $this->metaData[$key] = $value;
        return $this;
    }

    public function toString(): string
    {
        /** 1. create metadata for header, etc */
        $this->prepareMetaData();

        /**
         * 2. generate html from controller template
         */
        $controllerResult = call_user_func($this->controllerTemplate, $this->controllerData);

        /** 3. generate html document */
        return call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $controllerResult
        );
    }

    protected function prepareMetaData(): void
    {
        /**
         * Use this for add info to metaData
         * - $this->controllerData
         * - $this->controller
         * - $this->action
        */
    }
}
