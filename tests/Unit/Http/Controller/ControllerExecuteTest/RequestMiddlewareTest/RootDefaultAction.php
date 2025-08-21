<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;

final class RootDefaultAction extends AbstractAction implements DefaultActionInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $body     = $response->getBody();
        $body->write('<h1>Home page<h1>');
        $response = $response->withBody($body);
        return $response;
    }

    public function getDescription(): string
    {
        return 'Home';
    }
}
