<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Utils\Urlbuilder\StaticUrlbuilder;
use Romchik38\Server\Http\Utils\Urlbuilder\StaticUrlbuilderInterface;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;

abstract class AbstractControllerView extends AbstractView implements ControllerViewInterface
{
    protected DefaultViewDTOInterface|null $controllerData = null;
    protected ControllerInterface|null $controller         = null;
    protected string $action                               = '';

    public function __construct(
        ?MetaDataInterface $metaDataService = null,
    ) {
        parent::__construct($metaDataService);
    }

    public function setController(
        ControllerInterface $controller,
        string $action = ''
    ): self {
        $this->controller = $controller;
        $this->action     = $action;
        return $this;
    }

    public function setControllerData(DefaultViewDTOInterface $data): self
    {
        $this->controllerData = $data;
        return $this;
    }

    /**
     * @throws ViewBuildException
     * */
    protected function createStaticUrlbuilder(): StaticUrlbuilderInterface
    {
        if ($this->controller === null) {
            throw new ViewBuildException('Can\'t prepare static urlbuilder: controller was not set');
        }
        $path = new Path($this->controller->getFullPath($this->action));
        return new StaticUrlbuilder($path);
    }
}
