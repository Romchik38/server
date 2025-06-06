<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Dto;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Dto\ControllerDTO;

use function file_get_contents;
use function json_encode;

class ControllerDTOTest extends TestCase
{
    public function testGetName()
    {
        $dto = new ControllerDTO(
            'root',
            [],
            [],
            'Home'
        );

        $this->assertSame('root', $dto->getName());
    }

    public function testGetPath()
    {
        $dto = new ControllerDTO(
            'name',
            ['root'],
            [],
            'Some name'
        );

        $this->assertSame(['root'], $dto->getPath());
    }

    public function testGetChildren()
    {
        $aboutDto = new ControllerDTO(
            'about',
            ['root'],
            [],
            'About'
        );

        $rootDto = new ControllerDTO(
            'root',
            [],
            [$aboutDto],
            'Home'
        );

        $this->assertSame([$aboutDto], $rootDto->getChildren());
    }

    public function testJsonSerialize()
    {
        $aboutDto = new ControllerDTO(
            'about',
            ['root'],
            [],
            'About'
        );

        $rootDto = new ControllerDTO(
            'root',
            [],
            [$aboutDto],
            'Home'
        );

        $res = json_encode($rootDto);

        $expected = file_get_contents(__DIR__ . '/jsontext');

        $this->assertSame(
            $expected,
            $res
        );
    }
}
