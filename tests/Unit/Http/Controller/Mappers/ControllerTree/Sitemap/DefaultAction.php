<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\ControllerTree\Sitemap;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Mappers\ControllerTree\ControllerTreeInterface;

use function json_encode;

final class DefaultAction extends AbstractAction implements DefaultActionInterface
{
    public function __construct(
        protected readonly ControllerTreeInterface $controllerTreeService
    ) {
    }

    protected const DATA = [
        'description' => 'Sitemap page',
    ];

    public function execute(): ResponseInterface
    {
        $controllerDto = $this->controllerTreeService
            ->getOnlyLineRootControllerDTO($this->getController(), '');
        $response      = new Response();
        $body          = $response->getBody();
        $body->write(json_encode($controllerDto));
        return $response->withBody($body);
    }

    public function getDescription(): string
    {
        return $this::DATA['description'];
    }
}
