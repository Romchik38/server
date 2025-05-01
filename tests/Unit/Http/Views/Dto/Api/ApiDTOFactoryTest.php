<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\DTO\Api;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Views\Dto\Api\ApiDTO;
use Romchik38\Server\Http\Views\Dto\Api\ApiDTOFactory;

class ApiDTOFactoryTest extends TestCase
{
    public function testCreate()
    {
        $name        = 'some_name';
        $description = 'some_description';
        $status      = 'success';
        $result      = ['hello api'];

        $factory = new ApiDTOFactory();
        $dto     = $factory->create(
            $name,
            $description,
            $status,
            $result
        );

        $this->assertSame($name, $dto->getName());
        $this->assertSame($description, $dto->getDescription());
        $this->assertSame($status, $dto->getStatus());
        $this->assertSame($result, $dto->getResult());
        $this->assertSame(ApiDTO::class, $dto::class);
    }
}
