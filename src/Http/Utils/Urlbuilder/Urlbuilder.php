<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

class Urlbuilder extends AbstractUrlbuilder
{
    public function __construct(
        string $scheme = '',
        string $authority = ''
    ) {
        parent::__construct(new Target(), $scheme, $authority);
    }
}
