<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Catalog;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\AbstractAction;

final class DefaultAction extends AbstractAction implements DefaultActionInterface {
    protected const DATA = [
        'result' => '<h1>More than 20 000 products with great discount 50% for 1 day</h1>',
        'description' => 'Products catalog'
    ];

    public function execute(): ResponseInterface
    {
        $response = new Response();
        $body = $response->getBody();
        $body->write($this::DATA['result']);
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return $this::DATA['description'];
    }
}