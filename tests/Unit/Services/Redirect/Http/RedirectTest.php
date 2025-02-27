<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Redirect\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Romchik38\Server\Api\Models\Redirect\RedirectModelInterface;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTOFactory;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Model;
use Romchik38\Server\Services\Redirect\Http\CantCreateRedirectException;
use Romchik38\Server\Services\Redirect\Http\Redirect;

final class RedirectTest extends TestCase
{
    /**
     * redirect wasn't found in the database by provided url and method
     */
    public function testExecuteNoRedirect()
    {
        $redirectResultDtoFactory = $this->createMock(RedirectResultDTOFactory::class);
        $request                  = $this->createMock(ServerRequestInterface::class);

        $requestedUri = $this->createMock(UriInterface::class);
        $requestedUri->method('getScheme')->willReturn('http');
        $requestedUri->method('getHost')->willReturn('example.com');
        $request->method('getUri')->willReturn($requestedUri);

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

        $requestedUri = $this->createMock(UriInterface::class);
        $requestedUri->method('getScheme')->willReturn('http');
        $requestedUri->method('getHost')->willReturn('example.com');
        $request->method('getUri')->willReturn($requestedUri);

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

        $requestedUri = $this->createMock(UriInterface::class);
        $requestedUri->method('getScheme')->willReturn('');
        $requestedUri->method('getHost')->willReturn('');
        $request->method('getUri')->willReturn($requestedUri);

        $this->expectException(CantCreateRedirectException::class);

        $redirectModel      = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService    = new Redirect(
            $redirectRepository,
            $redirectResultDtoFactory,
            $request
        );
    }

    protected function createRepository(RedirectModelInterface $redirectModel): RedirectRepositoryInterface
    {
        return new class ($redirectModel) implements RedirectRepositoryInterface {
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
                    throw new NoSuchEntityException('no such entity in database');
                }
            }
        };
    }

    protected function createRedirectModel(
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
        ) extends Model implements RedirectModelInterface {
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
