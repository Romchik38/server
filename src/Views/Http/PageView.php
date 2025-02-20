<?php

declare(strict_types=1);

namespace Romchik38\Server\Views\Http;

use Closure;
use \Romchik38\Server\Api\Views\Http\HttpViewInterface;
use Romchik38\Server\Views\View;

class PageView extends View implements HttpViewInterface
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
        $html = call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $controllerResult
        );

        return $html;
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
