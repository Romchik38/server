<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;

class ControllerDTOTest extends TestCase
{
    public function testGetName()
    {
        $dto = new ControllerDTO(
            'root',
            [],
            []
        );

        $this->assertSame('root', $dto->getName());
    }

    public function testGetPath()
    {
        $dto = new ControllerDTO(
            'name',
            ['root'],
            []
        );

        $this->assertSame(['root'], $dto->getPath());
    }

    public function testGetChildren()
    {

        $aboutDto = new ControllerDTO(
            'about',
            ['root'],
            []
        );

        $rootDto = new ControllerDTO(
            'root',
            [],
            [$aboutDto]
        );

        $this->assertSame([$aboutDto], $rootDto->getChildren());
    }

    public function testJsonSerialize()
    {
        $aboutDto = new ControllerDTO(
            'about',
            ['root'],
            []
        );

        $rootDto = new ControllerDTO(
            'root',
            [],
            [$aboutDto]
        );

        $res = json_encode($rootDto);

        $this->assertSame(
            '{"name":"root","path":[],"children":[{"name":"about","path":["root"],"children":[]}]}',
            $res
        );
    }
}
