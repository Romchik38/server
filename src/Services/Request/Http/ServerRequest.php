<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\ServerRequestInterface;
use Romchik38\Server\Api\Services\Request\Http\ServerRequestServiceInterface;
use Romchik38\Server\Api\Services\Request\Http\UriFactoryInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    public function __construct(
        protected readonly UriFactoryInterface $uriFactory,
        protected readonly ServerRequestServiceInterface $serverRequestService
    )
    {
    }

    public function getParsedBody()
    {
        /** 1. retriving $_POST */
        $headers = $this->serverRequestService->getRequestHeaders();
        if ($headers !== false) {
            $contentType = $headers['Content-Type'] ?? '';
            if (
                ($contentType === 'application/x-www-form-urlencoded') ||
                ($contentType === 'multipart/form-data')
            ) {
                return $_POST;
            }
        }

        /** 2. No post data was provided, so sending body content */
        return $this->serverRequestService->getBodyContent();
    }
}
