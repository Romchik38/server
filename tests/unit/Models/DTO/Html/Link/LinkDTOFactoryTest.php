<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Html\Link\LinkDTOFactory;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

class LinkDTOFactoryTest extends TestCase
{
    public function testCreateThrowsExceptionBecauseName()
    {
        $name = '';
        $description = 'Home Page';
        $url = '/en';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }

    public function testCreateThrowsExceptionBecauseDescription()
    {
        $name = 'Home';
        $description = '';
        $url = '/en';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }

    public function testCreateThrowsExceptionBecauseUrl()
    {
        $name = 'Home';
        $description = '';
        $url = '';

        $this->expectException(InvalidArgumentException::class);

        $factory = new LinkDTOFactory();
        $factory->create($name, $description, $url);
    }
}
