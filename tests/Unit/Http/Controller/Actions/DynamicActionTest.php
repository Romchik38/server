<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Actions;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\ActionInterface;
use Romchik38\Server\Http\Controller\Actions\DynamicActionInterface;
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTO;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;

use function count;
use function sprintf;

class DynamicActionTest extends TestCase
{
    public function testExecute()
    {
        $uri     = new Uri('http://example.com/about');
        $request = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ActionInterface::TYPE_DYNAMIC_ACTION, 'about');

        $action   = $this->createAction();
        $response = $action->handle($request);
        $this->assertSame('<h1>About</h1>', (string) $response->getBody());
    }

    public function testExecuteThrowsNotFound(): void
    {
        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ActionInterface::TYPE_DYNAMIC_ACTION, 'contacts');

        $action = $this->createAction();
        $this->expectException(ActionNotFoundException::class);
        $action->handle($request);
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
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $dynamicRoute = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
                $result       = $this::DATA[$dynamicRoute] ?? null;
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
