<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Api;

use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

interface ApiDTOInterface extends DefaultViewDTOInterface
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR   = 'error';
    public const RESULT_FIELD   = 'result';
    public const STATUS_FIELD   = 'status';

    public function getResult(): mixed;

    public function getStatus(): string;
}
