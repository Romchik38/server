<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto\Api;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

interface ApiDTOInterface extends DefaultViewDTOInterface
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR   = 'error';
    public const RESULT_FIELD   = 'result';
    public const STATUS_FIELD   = 'status';

    public function getResult(): mixed;

    public function getStatus(): string;
}
