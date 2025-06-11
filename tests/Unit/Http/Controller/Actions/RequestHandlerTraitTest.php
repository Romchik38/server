<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Actions;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\RequestHandlerTrait;

final class RequestHandlerTraitTest extends TestCase
{
    public function testSerializeAcceptHeader(): void
    {
        $expectedHeaders = ['a/b', 'b/c'];
        $headerLine      = 'text/html,a/b;q=0.8';

        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET')->withHeader('accept', $headerLine);

        $action   = $this->createAction($expectedHeaders);
        $response = $action->handle($request);

        $this->assertSame('a/b', $response->getHeaderLine('content-type'));
    }

    public function testSerializeAcceptHeaderReturnMoreWeight(): void
    {
        $expectedHeaders = ['a/b', 'b/c'];
        $headerLine      = 'text/html,a/b;q=0.6,b/c;q=0.8';

        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET')->withHeader('accept', $headerLine);

        $action   = $this->createAction($expectedHeaders);
        $response = $action->handle($request);

        $this->assertSame('b/c', $response->getHeaderLine('content-type'));
    }

    public function testSerializeAcceptHeaderReturnNull(): void
    {
        $expectedHeaders = ['a/b', 'b/c'];
        $headerLine      = 'text/html';

        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET')->withHeader('accept', $headerLine);

        $action   = $this->createAction($expectedHeaders);
        $response = $action->handle($request);

        $this->assertSame(406, $response->getStatusCode());
    }

    private function createAction(array $expectedHeaders): RequestHandlerInterface
    {
        return new class ($expectedHeaders) extends AbstractAction {
            use RequestHandlerTrait;

            public function __construct(
                private readonly array $expectedHeaders
            ) {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $headerLine = $request->getHeaderLine('Accept');
                $result     = $this->serializeAcceptHeader($this->expectedHeaders, $headerLine);
                if ($result === null) {
                    return new TextResponse('Not Acceptable', 406);
                } else {
                    return new Response('some response')->withHeader('content-type', $result);
                }
            }
        };
    }
}
