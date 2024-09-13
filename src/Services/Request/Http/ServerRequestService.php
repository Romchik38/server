<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\ServerRequestBodyParserInterface;
use Romchik38\Server\Api\Services\Request\Http\ServerRequestServiceInterface;

class ServerRequestService implements ServerRequestServiceInterface
{
    public function __construct(
        protected ServerRequestBodyParserInterface|null $bodyParser = null
    ) {}

    /** 
     * Sending body content 
     * 
     * @return array|null null if no body content, otherwise parsed body
     * */
    public function getBodyContent(): array|null
    {
        $entityBody = file_get_contents('php://input');
        if ($entityBody === '') {
            return null;
        }
        if ($this->bodyParser === null) {
            return [$entityBody];
        } else {
            return $this->bodyParser->parseBody($entityBody);
        }
    }


    /**
     * @return false|array
     */
    public function getRequestHeaders(): array|bool
    {
        if (function_exists('apache_request_headers') === true) {
            $headers = apache_request_headers();
            if ($headers !== false) {
                return $headers;
            }
        }
        return false;
    }
}
