<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Root;

use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Errors\DynamicActionNotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

final class DynamicAction extends Action implements DynamicActionInterface
{

    protected array $data = [
        'about' => ['About page', '<h1>About our company</h1>'],
        'contacts' => ['Contacts page', '<h1>Company contacts page</h1>']
    ];

    protected const DESCRIPTION_INDEX = 0;
    protected const RESPONSE_INDEX = 1;

    public function execute(string $dynamicRoute): string
    {
        $arr = $this->data[$dynamicRoute] ?? null;
        if ($arr === null) throw new DynamicActionNotFoundException(
            sprintf('route %s not found', $dynamicRoute)
        );
        return $arr[$this::RESPONSE_INDEX];
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
