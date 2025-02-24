<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Controllers\Actions;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\AbstractAction;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

use function count;
use function sprintf;

class DynamicActionTest extends TestCase
{
    public function testExecute()
    {
        $action   = $this->createAction();
        $response = $action->execute('about');
        $this->assertSame('<h1>About</h1>', (string) $response->getBody());
    }

    public function testExecuteThrowsNotFound(): void
    {
        $action = $this->createAction();
        $this->expectException(ActionNotFoundException::class);
        $action->execute('contacts');
    }

    public function testGetDynamicRoutes(): void
    {
        $action    = $this->createAction();
        $routeDtos = $action->getDynamicRoutes();
        $this->assertSame(1, count($routeDtos));
    }

    public function testGetDescriptionThrowsException(): void
    {
        $action = $this->createAction();
        $this->expectException(DynamicActionLogicException::class);

        $action->getDescription('contacts');
    }

    protected function createAction(): DynamicActionInterface
    {
        return new class extends AbstractAction implements DynamicActionInterface {
            protected const DATA = ['about' => 'About'];
            public function execute(string $dynamicRoute): ResponseInterface
            {
                $result = $this::DATA[$dynamicRoute] ?? null;
                if ($result === null) {
                    throw new ActionNotFoundException(
                        sprintf(
                            'route %s not found',
                            $dynamicRoute
                        )
                    );
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write(sprintf('<h1>%s</h1>', $result));
                return $response->withBody($body);
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
                if ($description !== null) {
                    return $description;
                }

                throw new DynamicActionLogicException(sprintf(
                    'descruption for route %s not found',
                    $dynamicRoute
                ));
            }
        };
    }
}
