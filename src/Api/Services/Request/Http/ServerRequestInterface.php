<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\RequestInterface;

/**
 * PSR-7 Psr\Http\Message\ServerRequestInterface
 */
interface ServerRequestInterface extends RequestInterface
{
    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody();
}
