<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Products;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DynamicActionInterface;
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTO;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;

use function sprintf;

final class DynamicAction extends AbstractAction implements DynamicActionInterface
{
    protected array $data = [
        'product-1' => ['Product 1 page', '<h1>Product 1</h1>'],
        'product-2' => ['Product 2 page', '<h1>Product 2</h1>'],
    ];

    protected const DESCRIPTION_INDEX = 0;
    protected const RESPONSE_INDEX    = 1;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dynamicRoute = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
        $arr          = $this->data[$dynamicRoute] ?? null;
        if ($arr === null) {
            throw new ActionNotFoundException(
                sprintf('route %s not found', $dynamicRoute)
            );
        }
        $response = new Response();
        $body     = $response->getBody();
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
