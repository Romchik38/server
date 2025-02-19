<?php

declare(strict_types=1);

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;

class DefaultActionTest extends TestCase
{
    public function testExecute(): void
    {
        $action = $this->createDefaultAction();
        $response = $action->execute();
        $this->assertSame('result', (string) $response->getBody());
    }


    public function testGetDescription(): void
    {
        $action = $this->createDefaultAction();
        $this->assertSame('Some Description', $action->getDescription());
    }

    protected function createDefaultAction(): DefaultActionInterface
    {
        return new class extends Action implements DefaultActionInterface {
            public function execute(): ResponseInterface
            {
                $response = new Response();
                $body = $response->getBody();
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
