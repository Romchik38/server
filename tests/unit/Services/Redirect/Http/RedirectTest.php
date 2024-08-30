<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\RedirectModelInterface;
use Romchik38\Server\Services\Redirect\Http\Redirect;
use Romchik38\Server\Api\Models\RedirectRepositoryInterface;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTOFactory;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Model;

class RedirectTest extends TestCase
{
    private $scheme = 'http';
    private $host = 'somehost';
    private $url = 'someurl';
    private $statusCode = 301;
    private $redirectResultDTOFactory;
    private $redirectRepository;

    public function setUp(): void
    {
        $this->redirectResultDTOFactory = $this->createMock(RedirectResultDTOFactory::class);
    }

    protected function createRepository(RedirectModelInterface $redirectModel): RedirectRepositoryInterface
    {
        return new class($redirectModel) implements RedirectRepositoryInterface {
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

    protected  function createRedirectModel(string $url, int $statusCode): RedirectModelInterface
    {
        return new class($url, $statusCode) extends Model implements RedirectModelInterface {
            public function __construct(
                protected string $url,
                protected int $statusCode
            ) {}
            public function getRedirectTo(): string
            {
                return $this->url;
            }
            public function getRedirectCode(): int
            {
                return $this->statusCode;
            }
        };
    }

    public function testExecuteNoRedirect(){
        
    }
}
