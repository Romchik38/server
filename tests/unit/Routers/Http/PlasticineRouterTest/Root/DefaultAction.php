<?php

declare(strict_types=1);

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;

final class DefaultAction extends Action implements DefaultActionInterface
{
    public function execute(): ResponseInterface
    {
        $response = new Response();
        $body = $response->getBody();
        $body->write('hello world');
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return 'Home Page';
    }
}
