<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TextResponseHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $response = 'hello world'
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new TextResponse($this->response);
    }
}
