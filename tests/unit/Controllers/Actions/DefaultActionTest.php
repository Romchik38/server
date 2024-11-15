<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;

class DefaultActionTest extends TestCase
{
    public function testExecute(): void
    {
        $action = $this->createDefaultAction();
        $this->assertSame('result', $action->execute());
    }


    public function testGetDescription(): void
    {
        $action = $this->createDefaultAction();
        $this->assertSame('Some Description', $action->getDescription());
    }

    protected function createDefaultAction(): DefaultActionInterface
    {
        return new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return 'result';
            }

            public function getDescription(): string
            {
                return 'Some Description';
            }
        };
    }
}
