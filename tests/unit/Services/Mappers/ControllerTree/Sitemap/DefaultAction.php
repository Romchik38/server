<?php

declare(strict_types=1);

namespace Romchik38\Tests\Services\Mappers\ControllerTree\Sitemap;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

final class DefaultAction extends Action implements DefaultActionInterface
{
    public function __construct(
        protected readonly ControllerTreeInterface $controllerTreeService
    ) {}
    protected const DATA = [
        'description' => 'Sitemap page'
    ];

    public function execute(): ResponseInterface
    {
        $controllerDTO = $this->controllerTreeService
            ->getOnlyLineRootControllerDTO($this->getController(), '');
        $response = new Response();
        $body = $response->getBody();
        $body->write(json_encode($controllerDTO));
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return $this::DATA['description'];
    }
}
