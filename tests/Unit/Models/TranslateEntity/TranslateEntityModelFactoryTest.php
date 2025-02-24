<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\TranslateEntity;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModelFactory;

class TranslateEntityModelFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new TranslateEntityModelFactory();
        $model = $factory->create();

        $this->assertEquals([], $model->getAllData());
    }
}
