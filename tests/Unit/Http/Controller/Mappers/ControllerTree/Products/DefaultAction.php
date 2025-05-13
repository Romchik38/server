<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Products;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;

final class DefaultAction extends AbstractAction implements DefaultActionInterface
{
    protected const DATA = [
        'result'      => '<h1>More than 20 000 products with great discount 50% for 1 day</h1>',
        'description' => 'Products catalog',
    ];

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $body     = $response->getBody();
        $body->write($this::DATA['result']);
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return $this::DATA['description'];
    }
}
