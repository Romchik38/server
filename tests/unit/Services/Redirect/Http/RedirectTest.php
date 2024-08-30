<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\Redirect\RedirectModelInterface;
use Romchik38\Server\Services\Redirect\Http\Redirect;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTOFactory;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Model;
use Romchik38\Server\Models\Sql\Repository;

class RedirectTest extends TestCase
{
    private $scheme = 'http';
    private $host = 'somehost';
    private $url = 'someurl';
    private $method = 'GET';
    private $statusCode = 301;
    private $redirectResultDTOFactory;

    public function setUp(): void
    {
        $this->redirectResultDTOFactory = $this->createMock(RedirectResultDTOFactory::class);
    }

    public function testExecuteNoRedirect(){

        $redirectModel = $this->createRedirectModel('/hello', 301, 'GET');
        $redirectRepository = $this->createRepository($redirectModel);
        $redirectService = new Redirect(
            $redirectRepository,
            $this->redirectResultDTOFactory
        );

        //$result = $redirectService->execute($this->url, $this->method);

        //$this->assertEquals(null, $result);
    }

    protected function createRepository(RedirectModelInterface $redirectModel): RedirectRepositoryInterface
    {
        return new class($redirectModel) extends Repository implements RedirectRepositoryInterface {
            public function __construct(
                protected RedirectModelInterface $redirectModel
            ) {}
            public function checkUrl(string $url, string $method): RedirectModelInterface {
                if ($this->redirectModel->getRedirectTo() === $url && 
                    $this->redirectModel->getRedirectCode === $method) {
                    return $this->redirectModel;
                } else {
                    throw new NoSuchEntityException('no such entity in database');
                }
            }
        };
    }

    protected  function createRedirectModel(string $url, int $statusCode, string $method): RedirectModelInterface
    {
        return new class($url, $statusCode, $method) extends Model implements RedirectModelInterface {
            public function __construct(
                protected string $url,
                protected int $statusCode,
                protected string $method,
            ) {}
            public function getRedirectTo(): string
            {
                return $this->url;
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
