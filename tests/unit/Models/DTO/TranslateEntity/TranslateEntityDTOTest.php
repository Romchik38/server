<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTO;

class TranslateEntityDTOTest extends TestCase
{

    protected $key = 'some.key';
    protected $data = [
        'en' => 'some phrase',
        'uk' => 'якась фраза'
    ];

    public function testGetKey()
    {
        $dto = new TranslateEntityDTO($this->key, $this->data);

        $this->assertSame($this->key, $dto->getKey());
    }

    public function testPhrase()
    {
        $dto = new TranslateEntityDTO($this->key, $this->data);

        $this->assertSame('some phrase', $dto->getPhrase('en'));
    }
}
