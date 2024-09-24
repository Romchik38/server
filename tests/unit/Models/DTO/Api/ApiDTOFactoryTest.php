<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Api\ApiDTOFactory;
use Romchik38\Server\Models\DTO\Api\ApiDTO;

class ApiDTOFactoryTest extends TestCase
{
    public function testCreate()
    {

        $name = 'some_name';
        $description = 'some_description';
        $status = 'success';
        $result = ['hello api'];

        $factory = new ApiDTOFactory();
        $dto = $factory->create(
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
