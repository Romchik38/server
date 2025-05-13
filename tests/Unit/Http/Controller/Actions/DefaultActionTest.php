<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Actions;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;

class DefaultActionTest extends TestCase
{
    public function testExecute(): void
    {
        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET');

        $action   = $this->createDefaultAction();
        $response = $action->handle($request);
        $this->assertSame('result', (string) $response->getBody());
    }

    public function testGetDescription(): void
    {
        $action = $this->createDefaultAction();
        $this->assertSame('Some Description', $action->getDescription());
    }

    protected function createDefaultAction(): DefaultActionInterface
    {
        return new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('result');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Some Description';
            }
        };
    }
}
