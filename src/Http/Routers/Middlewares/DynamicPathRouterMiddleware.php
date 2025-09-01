<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Http\Routers\Middlewares\Result\DynamicPathMiddlewareResult;

use function array_search;
use function array_shift;
use function count;
use function is_array;
use function is_string;

class DynamicPathRouterMiddleware extends AbstractPathRouterMiddleware
{
    protected const SCHEME_HOST_DELIMITER = '://';

    public function __construct(
        protected DynamicRootInterface $dynamicRootService,
        protected ResponseFactoryInterface $responseFactory,
        string $attributeName = 'dynamic_path_router_middleware',
        protected string $prefferedAttributeName = 'preffered_root_router_middleware',
    ) {
        parent::__construct($attributeName);
    }

    /** @return DynamicPathMiddlewareResult|ResponseInterface|null */
    public function __invoke(ServerRequestInterface $request): mixed
    {
        $uri               = $request->getUri();
        $scheme            = $uri->getScheme();
        $authority         = $uri->getAuthority();
        $path              = $uri->getPath();
        $defaultRoot       = $this->dynamicRootService->getDefaultRoot();
        $rootList          = $this->dynamicRootService->getRootNames();
        $prefferedRootList = $this->preparePrefferedList($request->getAttribute($this->prefferedAttributeName));

        $parts = $this->createParts($request);

        // delete first blank item
        array_shift($parts);

        // replace default with preffered if exist
        $redirectRoot  = $defaultRoot->getName();
        $prefferedRoot = $this->prefferedRoot($prefferedRootList, $rootList);
        if ($prefferedRoot !== null) {
            $redirectRoot = $prefferedRoot;
        }

        // 2. for / redirect to default root
        if (count($parts) === 0) {
            $redirectLine = $scheme
                . $this::SCHEME_HOST_DELIMITER
                . $authority
                . '/'
                . $redirectRoot;
            return ($this->responseFactory->createResponse(301))
                ->withHeader('Location', $redirectLine);
        }

        $rootName = $parts[0];

        // 3. try to redirect to defaultRoot + path
        if (array_search($rootName, $rootList, true) === false) {
            $redirectLinePlusPath = $scheme
                . $this::SCHEME_HOST_DELIMITER
                . $authority
                . '/'
                . $redirectRoot
                . $path;
            return ($this->responseFactory->createResponse(301))
                ->withHeader('Location', $redirectLinePlusPath);
        }

        $this->dynamicRootService->setCurrentRoot($rootName);
        $dynamicRoot = $this->dynamicRootService->withCurrentRoot($rootName);
        $parts[0]    = ControllerInterface::ROOT_NAME;

        try {
            $path = new Path($parts);
        } catch (InvalidArgumentException) {
            return null;
        }

        return new DynamicPathMiddlewareResult($path, $dynamicRoot);
    }

    /**
     * @param array<int,string> $preffered
     * @param array<int,string> $rootList
     */
    protected function prefferedRoot($preffered, $rootList): ?string
    {
        foreach ($preffered as $prefferedRoot) {
            if (array_search($prefferedRoot, $rootList, true) !== false) {
                return $prefferedRoot;
            }
        }
        return null;
    }

    /**
     * @param mixed $list
     * @return array<int,string>
     */
    protected function preparePrefferedList($list): array
    {
        $filtered = [];
        if (! is_array($list)) {
            return $filtered;
        }
        foreach ($list as $item) {
            if (is_string($item)) {
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
}
