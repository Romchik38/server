<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\Link;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Mappers\Link\LinkDTOFactory;

class LinkDTOFactoryTest extends TestCase
{
    public function testCreateThrowsExceptionBecauseName()
    {
        $name        = '';
        $description = 'Home Page';
        $url         = '/en';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }

    public function testCreateThrowsExceptionBecauseDescription()
    {
        $name        = 'Home';
        $description = '';
        $url         = '/en';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }

    public function testCreateThrowsExceptionBecauseUrl()
    {
        $name        = 'Home';
        $description = '';
        $url         = '';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }
}
