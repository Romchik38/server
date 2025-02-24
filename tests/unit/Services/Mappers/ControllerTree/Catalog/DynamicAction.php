<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Catalog;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Actions\AbstractAction;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

final class DynamicAction extends AbstractAction implements DynamicActionInterface
{
    public function __construct(
        protected readonly ControllerTreeInterface $controllerTreeService
    ) {}

    protected array $data = [
        'product-1' => ['Product 1 page', '<h1>Product 1</h1>'],
        'product-2' => ['Product 2 page', '<h1>Product 2</h1>']
    ];

    protected const DESCRIPTION_INDEX = 0;
    protected const RESPONSE_INDEX = 1;

    public function execute(string $dynamicRoute): ResponseInterface
    {
        $arr = $this->data[$dynamicRoute] ?? null;
        if ($arr === null) throw new ActionNotFoundException(
            sprintf('route %s not found', $dynamicRoute)
        );

        $controllerDTO = $this->controllerTreeService
            ->getOnlyLineRootControllerDTO($this->getController(), $dynamicRoute);
        $response = new Response();
        $body = $response->getBody();
        $body->write(json_encode($controllerDTO));
        return $response->withBody($body);
    }

    public function getDynamicRoutes(): array
    {
        $routes = [];
        foreach ($this->data as $route => $arr) {
            $routes[] = new DynamicRouteDTO($route, $arr[$this::DESCRIPTION_INDEX]);
        }
        return $routes;
    }

    public function getDescription(string $dynamicRoute): string
    {
        return $this->data[$dynamicRoute][$this::DESCRIPTION_INDEX];
    }
}
