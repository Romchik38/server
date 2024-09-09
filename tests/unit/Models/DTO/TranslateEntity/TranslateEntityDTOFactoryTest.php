<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTOFactory;

class TranslateEntityDTOFactoryTest extends TestCase
{
    public function testCreate()
    {
        $key = 'some.key';
        $data = [
            'en' => 'some phrase',
            'uk' => 'якась фраза'
        ];

        $factory = new TranslateEntityDTOFactory();
        $dto = $factory->create($key, $data);

        $this->assertSame($key, $dto->getKey());
        $this->assertSame($data, $dto->getAllData());
    }
}
