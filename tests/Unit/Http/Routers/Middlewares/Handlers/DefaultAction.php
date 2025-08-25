<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;

final class DefaultAction extends AbstractAction implements DefaultActionInterface
{
    public function __construct(
        public readonly string $responsePhrase
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new TextResponse($this->responsePhrase);
    }

    public function getDescription(): string
    {
        return 'Home';
    }
}
