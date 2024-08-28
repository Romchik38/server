<?php

declare(strict_types=1);

namespace Romchik38\Server\Views\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use \Romchik38\Server\Api\Views\Http\HttpViewInterface;
use Romchik38\Site1\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

class PageView implements HttpViewInterface
{
    protected string $controllerData = '';
    protected array $metaData = [];
    protected ControllerInterface|null $controller = null;
    protected string $action;

    public function __construct(
        protected $generateTemplate,
        protected $controllerTemplate
        )
    {
    }

    public function setController(ControllerInterface $controller, string $action = ''): HttpViewInterface {
        $this->controller = $controller;
        $this->action = $action;
        return $this;
    }

    public function setControllerData(DefaultViewDTOInterface $data): HttpViewInterface
    {
        $this->controllerData = call_user_func($this->controllerTemplate, $data);
        $this->prepareMetaData($data);
        return $this;
    }

    public function setMetadata(string $key, string $value): HttpViewInterface
    {
        $this->metaData[$key] = $value;
        return $this;
    }

    public function toString(): string
    {
        return $this->build();
    }

    protected function build(): string
    {

        $html = call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $this->controllerData
        );

        return $html;
    }

    protected function prepareMetaData(DefaultViewDTOInterface $data): void{
        /** use this for add info to metaData */
    }
}
