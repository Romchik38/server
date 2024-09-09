<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Translate;

interface TranslateInterface
{
    public function t(string $str): string;
}
