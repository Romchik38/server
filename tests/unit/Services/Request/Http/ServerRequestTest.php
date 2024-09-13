<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Services\Request\Http\ServerRequestServiceInterface;
use Romchik38\Server\Services\Request\Http\UriFactory;
use Romchik38\Server\Services\Request\Http\ServerRequest;

class ServerRequestTest extends TestCase
{
    public function testGetParsedBodyNoPostData()
    {
        $service = new class() implements ServerRequestServiceInterface {
            public function getRequestHeaders(): array|bool
            {
                return false;
            }
            public function getBodyContent(): array|null
            {
                return ['hello'];
            }
        };

        $serverRequest = new ServerRequest(new UriFactory(), $service);
        $this->assertSame(['hello'], $serverRequest->getParsedBody());
    }

    public function testGetParsedBodyReturnPostDataWithoutContentType()
    {
        $service = new class() implements ServerRequestServiceInterface {
            public function getRequestHeaders(): array|bool
            {
                return [];
            }
            public function getBodyContent(): array|null
            {
                return ['hello'];
            }
        };

        $serverRequest = new ServerRequest(new UriFactory(), $service);
        $this->assertSame(['hello'], $serverRequest->getParsedBody());
    }

    public function testGetParsedBodyReturnPostDataWithContentTypeButWithoutTypes()
    {
        $service = new class() implements ServerRequestServiceInterface {
            public function getRequestHeaders(): array|bool
            {
                return ['Content-Type' => 'text/html'];
            }
            public function getBodyContent(): array|null
            {
                return ['hello'];
            }
        };

        $serverRequest = new ServerRequest(new UriFactory(), $service);
        $this->assertSame(['hello'], $serverRequest->getParsedBody());
    }

    public function testGetParsedBodyReturnPostDataWithTypeXwwwformurlencoded()
    {
        $_POST = ['key1' => 'val1'];
        $service = new class() implements ServerRequestServiceInterface {
            public function getRequestHeaders(): array|bool
            {
                return ['Content-Type' => 'application/x-www-form-urlencoded'];
            }
            public function getBodyContent(): array|null
            {
                return ['hello'];
            }
        };

        $serverRequest = new ServerRequest(new UriFactory(), $service);
        $this->assertSame($_POST, $serverRequest->getParsedBody());
    }

    public function testGetParsedBodyReturnPostDataWithTypeMultipartformdata()
    {
        $_POST = ['key1' => 'val1'];
        $service = new class() implements ServerRequestServiceInterface {
            public function getRequestHeaders(): array|bool
            {
                return ['Content-Type' => 'application/x-www-form-urlencoded'];
            }
            public function getBodyContent(): array|null
            {
                return ['hello'];
            }
        };

        $serverRequest = new ServerRequest(new UriFactory(), $service);
        $this->assertSame($_POST, $serverRequest->getParsedBody());
    }
}
