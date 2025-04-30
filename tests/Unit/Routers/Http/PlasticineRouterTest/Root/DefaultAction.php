<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Routers\Http\PlasticineRouterTest\Root;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;

final class DefaultAction extends AbstractAction implements DefaultActionInterface
{
    public function execute(): ResponseInterface
    {
        $response = new Response();
        $body     = $response->getBody();
        $body->write('hello world');
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return 'Home Page';
    }
}
