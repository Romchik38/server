<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

use function explode;

class PrefferedRootRouterMiddlewareUseAcceptLanguage extends AbstractPrefferedRootRouterMiddleware
{
    /** @return array<int,string> */
    public function __invoke(ServerRequestInterface $request): array
    {
        $headerLine = $request->getHeaderLine('Accept-Language');
        $values     = explode(',', $headerLine);
        $result     = [];
        foreach ($values as $value) {
            $parts = explode(';', $value);
            $type  = $parts[0];
            if ($type === '*/*' || $type === '') {
                continue;
            }
            $result[] = $type;
        }
        return $result;
    }
}
