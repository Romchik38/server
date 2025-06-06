<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\Session\Http;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Utils\Session\Session;

use function session_destroy;

class SessionTest extends TestCase
{
    public function testGetData()
    {
        $key   = 'id';
        $value = '100';

        $session = new Session();

        $_SESSION[$key] = $value;

        $this->assertSame($value, $session->getData($key));

        session_destroy();
    }

    public function testGetAllData()
    {
        $key   = 'id';
        $value = '100';

        $session = new Session();

        $_SESSION[$key] = $value;

        $this->assertSame(['id' => '100'], $session->getAllData());

        session_destroy();
    }

    public function testSetData()
    {
        $key   = 'id';
        $value = '100';

        $session = new Session();

        $session->setData($key, $value);

        $this->assertSame($value, $_SESSION[$key]);

        session_destroy();
    }

    public function testLogout()
    {
        $key   = 'id';
        $value = '100';

        $session = new Session();

        $_SESSION[$key] = $value;

        $session->logout();

        $this->assertSame([], $_SESSION);
    }
}
