<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Handlers\Redirect;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\CantCreateRedirectException;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\NoSuchRedirectException;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Redirect;
use Romchik38\Server\Http\Routers\Handlers\Redirect\RedirectModelInterface;
use Romchik38\Server\Http\Routers\Handlers\Redirect\RedirectResultDTO;
use Romchik38\Server\Http\Routers\Handlers\Redirect\RedirectResultDTOFactory;
use Romchik38\Server\Http\Routers\Handlers\Redirect\RepositoryInterface;

final class RedirectTest extends TestCase
{
    /**
     * redirect wasn't found in the database by provided url and method
     */
    public function testExecuteNoRedirect()
    {
        $redirectResultDtoFactory = $this->createMock(RedirectResultDTOFactory::class);
        $request                  = $this->createMock(ServerRequestInterface::class);

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $redirectModel      = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService    = new Redirect(
            $redirectRepository,
            $redirectResultDtoFactory,
            $request
        );

        $result = $redirectService->execute('/hello', 'GET');

        $this->assertEquals(null, $result);
    }

    /**
     * redirect was found in the database by provided url and method
     */
    public function testExecuteFindRedirect()
    {
        $redirectResultDtoFactory = $this->createMock(RedirectResultDTOFactory::class);
        $request                  = $this->createMock(ServerRequestInterface::class);

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $redirectResultDto = new RedirectResultDTO('http://example.com/', 301);
        $redirectResultDtoFactory->expects($this->once())
            ->method('create')->with('http://example.com/', 301)
            ->willReturn($redirectResultDto);

        $redirectModel      = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService    = new Redirect(
            $redirectRepository,
            $redirectResultDtoFactory,
            $request
        );

        $result = $redirectService->execute('/index', 'GET');

        $this->assertSame('http://example.com/', $result->getRedirectLocation());
        $this->assertSame(301, $result->getStatusCode());
    }

    /**
     * __construct method checks host and schema from request and throws an error
     */
    public function testConstructWithEmptySchemaHost()
    {
        $redirectResultDtoFactory = $this->createMock(RedirectResultDTOFactory::class);
        $request                  = $this->createMock(ServerRequestInterface::class);

        $uri     = new Uri('/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $this->expectException(CantCreateRedirectException::class);

        $redirectModel      = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        new Redirect(
            $redirectRepository,
            $redirectResultDtoFactory,
            $request
        );
    }

    private function createRepository(RedirectModelInterface $redirectModel): RepositoryInterface
    {
        return new class ($redirectModel) implements RepositoryInterface {
            public function __construct(
                protected RedirectModelInterface $redirectModel
            ) {
            }

            public function checkUrl(string $redirectFrom, string $method): RedirectModelInterface
            {
                if (
                    $this->redirectModel->getRedirectFrom() === $redirectFrom &&
                    $this->redirectModel->getRedirectMethod() === $method
                ) {
                    return $this->redirectModel;
                } else {
                    throw new NoSuchRedirectException('no such entity in database');
                }
            }
        };
    }

    private function createRedirectModel(
        string $redirectFrom,
        string $redirectTo,
        int $statusCode,
        string $method
    ): RedirectModelInterface {
        return new class (
            $redirectFrom,
            $redirectTo,
            $statusCode,
            $method
        ) implements RedirectModelInterface {
            public function __construct(
                protected string $redirectFrom,
                protected string $redirectTo,
                protected int $statusCode,
                protected string $method,
            ) {
            }

            public function getRedirectFrom(): string
            {
                return $this->redirectFrom;
            }

            public function getRedirectTo(): string
            {
                return $this->redirectTo;
            }

            public function getRedirectCode(): int
            {
                return $this->statusCode;
            }

            public function getRedirectMethod(): string
            {
                return $this->method;
            }
        };
    }
}
