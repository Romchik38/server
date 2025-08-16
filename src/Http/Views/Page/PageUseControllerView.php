<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Page;

use Closure;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\Breadcrumb;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbInterface;
use Romchik38\Server\Http\Controller\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Http\Views\AbstractControllerView;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;
use Romchik38\Server\Http\Views\MetaDataInterface;
use Romchik38\Server\Http\Views\Traits\BreadcrumbControllerTrait;

use function call_user_func;

class PageUseControllerView extends AbstractControllerView
{
    use BreadcrumbControllerTrait;

    protected readonly BreadcrumbInterface $breadcrumbService;

    public function __construct(
        protected readonly Closure $generateTemplate,
        protected readonly Closure $controllerTemplate,
        ?BreadcrumbInterface $breadcrumbService = null,
        ?MetaDataInterface $metaDataService = null,
    ) {
        if ($breadcrumbService !== null) {
            $this->breadcrumbService = $breadcrumbService;
        } else {
            $this->breadcrumbService = new Breadcrumb(new ControllerTree());
        }
        parent::__construct($metaDataService);
    }

    public function toString(): string
    {
        if (
            $this->controllerData === null ||
            $this->controller === null
        ) {
            throw new ViewBuildException('View build aborted - controller(data) was not set');
        }

        /** 1. create metadata for header, etc */
        $this->prepareMetaData();
        $this->metaData['breadcrumbs']       = $this->prepareBreadcrumbs();
        $this->metaData['static_urlbuilder'] = $this->createStaticUrlbuilder();

        /**
         * 2. generate html from a controller template
         */
        $controllerResult = call_user_func(
            $this->controllerTemplate,
            $this->metaData,
            $this->controllerData,
            $this->action
        );

        /** 3. generate html document */
        return call_user_func(
            $this->generateTemplate,
            $this->metaData,
            $controllerResult
        );
    }
}
