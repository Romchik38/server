<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Request\Http;

/**
 * @todo replace this with  
 *  Psr\Http\Message\ServerRequestInterface extends 
 *      Psr\Http\Message\RequestInterface extends 
 *          Psr\Http\Message\MessageInterface
 */
interface RequestInterface
{

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string;

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri(): UriInterface;
}
