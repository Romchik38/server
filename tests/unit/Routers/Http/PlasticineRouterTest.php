<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Routers\Http\PlasticineRouter;
use Romchik38\Server\Results\Http\HttpRouterResult;

class PlasticineRouterTest extends TestCase
{

    protected $routerResult;
    protected array $controllers;
    protected $controller;
    protected array $headers = [];
    protected $notFoundController = null;
    protected $redirectService = null;

    public function setUp(): void
    {
        $this->routerResult = $this->createMock(HttpRouterResult::class);
        $this->controller = $this->createMock(Controller::class);
        $this->controllers = ['GET' => $this->controller];
    }
    public function testExecuteRedirect() {



        $router = new PlasticineRouter(
            $this->routerResult,
            $this->controllers,
            $this->headers,
            $this->notFoundController,
            $this->redirectService
        );
    }
}
