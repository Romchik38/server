<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Api;

use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

interface ApiDTOInterface extends DefaultViewDTOInterface
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const RESULT_FIELD = 'result';
    const STATUS_FIELD = 'status';

    public function getResult(): mixed;
    public function getStatus(): string;
}
