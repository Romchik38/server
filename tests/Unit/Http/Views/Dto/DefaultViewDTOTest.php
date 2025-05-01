<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Dto;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;

use function file_get_contents;
use function json_encode;

final class DefaultViewDTOTest extends TestCase
{
    public function testGets(): void
    {
        $name        = 'some name 1';
        $description = 'some description 1';
        $dto         = new DefaultViewDTO($name, $description);

        $this->assertSame($name, $dto->getName());
        $this->assertSame($description, $dto->getDescription());
    }

    public function testSerialize(): void
    {
        $json        = file_get_contents(__DIR__ . '/serialize.json');
        $name        = 'some name 1';
        $description = 'some description 1';
        $dto         = new DefaultViewDTO($name, $description);

        $this->assertSame($json, json_encode($dto));
    }
}
