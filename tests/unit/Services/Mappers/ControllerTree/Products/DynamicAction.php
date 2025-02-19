<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Products;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

final class DynamicAction extends Action implements DynamicActionInterface
{

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
        $response = new Response();
        $body = $response->getBody();
        $body->write($arr[$this::RESPONSE_INDEX]);
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
