<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\DTO\Controller;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;

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

        $this->assertSame(
            '{"name":"root","path":[],"children":[{"name":"about","path":["root"],"children":[],"description":"About"}],"description":"Home"}',
            $res
        );
    }
}
