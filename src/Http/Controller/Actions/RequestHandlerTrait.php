<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use function array_search;
use function explode;
use function substr;

trait RequestHandlerTrait
{
    /**
     * @param array<int,string> $expectedHeaders - Example ['text/html', 'application/json']
     * */
    private function serializeAcceptHeader(
        array $expectedHeaders,
        string $headerLine,
        string $all = 'text/html'
    ): ?string {
        if ($headerLine === '') {
            return null;
        }

        $preferedType  = '';
        $preferedValue = 0;
        $values        = explode(',', $headerLine);
        foreach ($values as $value) {
            $parts = explode(';', $value);
            $type  = $parts[0];
            if ($type === '*/*') {
                $type = $all;
            }
            $q = $parts[1] ?? null;
            if ($q === null) {
                $cost = 1;
            } else {
                $cost = (float) substr($q, 2);
            }
            $serializedType = array_search($type, $expectedHeaders);
            if ($serializedType === false) {
                continue;
            } else {
                if ($cost > $preferedValue) {
                    $preferedType = $type;
                }
            }
        }
        if ($preferedType !== '') {
            return $preferedType;
        } else {
            return null;
        }
    }
}
