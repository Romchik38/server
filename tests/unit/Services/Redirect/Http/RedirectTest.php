<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\Redirect\RedirectModelInterface;
use Romchik38\Server\Services\Redirect\Http\Redirect;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTOFactory;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Model;
use Romchik38\Server\Models\Sql\Repository;
use Romchik38\Server\Services\Request\Http\Request;
use Romchik38\Server\Services\Request\Http\Uri;

class RedirectTest extends TestCase
{
    // private $scheme = 'http';
    // private $host = 'example.com';
    // private $url = 'someurl';
    // private $method = 'GET';
    // private $statusCode = 301;
    private $redirectResultDTOFactory;
    private $request;

    public function setUp(): void
    {
        $this->redirectResultDTOFactory = $this->createMock(RedirectResultDTOFactory::class);
        $this->request = $this->createMock(Request::class);
    }

    /**
     * redirect wasn't found in the database by provided url and method
     */
    public function testExecuteNoRedirect()
    {
        $requestedUri = new Uri('http', 'example.com');
        $this->request->method('getUri')->willReturn($requestedUri);

        $redirectModel = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService = new Redirect(
            $redirectRepository,
            $this->redirectResultDTOFactory,
            $this->request
        );

        $result = $redirectService->execute('/hello', 'GET');

        $this->assertEquals(null, $result);
    }

    /**
     * redirect was found in the database by provided url and method
     */
    public function testExecuteFindRedirect() {
        $requestedUri = new Uri('http', 'example.com');
        $this->request->method('getUri')->willReturn($requestedUri);

        $redirectResultDTO = new RedirectResultDTO('http://example.com/', 301);
        $this->redirectResultDTOFactory->expects($this->once())
            ->method('create')->with('http://example.com/', 301)
            ->willReturn($redirectResultDTO);

        $redirectModel = $this->createRedirectModel('/index', '/', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService = new Redirect(
            $redirectRepository,
            $this->redirectResultDTOFactory,
            $this->request
        );

        $result = $redirectService->execute('/index', 'GET');

        $this->assertSame('http://example.com/', $result->getRedirectLocation());
        $this->assertSame(301, $result->getStatusCode());
    }

    protected function createRepository(RedirectModelInterface $redirectModel): RedirectRepositoryInterface
    {
        return new class($redirectModel) implements RedirectRepositoryInterface {
            public function __construct(
                protected RedirectModelInterface $redirectModel
            ) {}
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

    protected  function createRedirectModel(
        string $redirectFrom,
        string $redirectTo, 
        int $statusCode, 
        string $method
    ): RedirectModelInterface
    {
        return new class($redirectFrom, $redirectTo, $statusCode, $method) extends Model implements RedirectModelInterface {
            public function __construct(
                protected string $redirectFrom,
                protected string $redirectTo,
                protected int $statusCode,
                protected string $method,
            ) {}
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
