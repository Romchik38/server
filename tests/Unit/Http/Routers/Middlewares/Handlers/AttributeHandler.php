<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class AttributeHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly string $attributeName = 'attribute_1'
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $value = $request->getAttribute($this->attributeName) ?? null;
        if ($value === null) {
            throw new RuntimeException('Attribute name does not set');
        }
        return new TextResponse($value);
    }
}
