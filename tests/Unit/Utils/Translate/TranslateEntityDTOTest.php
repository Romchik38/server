<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Translate;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Utils\Translate\TranslateEntityDTO;

class TranslateEntityDTOTest extends TestCase
{
    protected string $key = 'some.key';
    protected array $data = [
        'en' => 'some phrase',
        'uk' => 'якась фраза',
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
