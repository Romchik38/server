<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\DynamicActionNotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

class DynamicActionTest extends TestCase
{
    public function testExecute()
    {
        $action = $this->createAction();
        $result = $action->execute('about');
        $this->assertSame('<h1>About</h1>', $result);
    }

    public function testExecuteThrowsNotFound(): void
    {
        $action = $this->createAction();
        $this->expectException(DynamicActionNotFoundException::class);
        $action->execute('contacts');
    }

    public function testGetDynamicRoutes(): void
    {
        $action = $this->createAction();
        $routeDTOs = $action->getDynamicRoutes();
        $this->assertSame(1, count($routeDTOs));
    }

    public function testGetDescriptionThrowsException(): void
    {
        $action = $this->createAction();
        $this->expectException(DynamicActionLogicException::class);

        $action->getDescription('contacts');
    }

    protected function createAction(): DynamicActionInterface
    {
        return new class extends Action implements DynamicActionInterface {
            protected const DATA = ['about' => 'About'];
            public function execute(string $dynamicRoute): string
            {
                $response = $this::DATA[$dynamicRoute] ?? null;
                if ($response === null) {
                    throw new DynamicActionNotFoundException(
                        sprintf(
                            'route %s not found',
                            $dynamicRoute
                        )
                    );
                }
                return sprintf('<h1>%s</h1>', $response);
            }
            public function getDynamicRoutes(): array
            {
                $routes = [];
                foreach ($this::DATA as $route => $description) {
                    $routes[] = new DynamicRouteDTO($route, $description);
                }
                return $routes;
            }

            public function getDescription(string $dynamicRoute): string
            {
                $description = $this::DATA[$dynamicRoute] ?? null;
                if ($description !== null) return $description;

                throw new DynamicActionLogicException(sprintf(
                    'descruption for route %s not found',
                    $dynamicRoute
                ));
            }
        };
    }
}
