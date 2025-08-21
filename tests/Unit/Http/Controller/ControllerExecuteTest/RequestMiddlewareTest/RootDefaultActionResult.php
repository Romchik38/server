<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest\AbstractRequestMiddleware;

use function sprintf;

final class RootDefaultActionResult extends AbstractAction implements DefaultActionInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userName = $request->getAttribute(AbstractRequestMiddleware::ATTRIBUTE_NAME);
        $response = new Response();
        $body     = $response->getBody();
        $body->write(sprintf('<h1>Hello user %s<h1>', $userName));
        $response = $response->withBody($body);
        return $response;
    }

    public function getDescription(): string
    {
        return 'Home';
    }
}
