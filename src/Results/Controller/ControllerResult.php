<?php

declare(strict_types=1);

namespace Romchik38\Server\Results\Controller;

use Romchik38\Server\Api\Results\Controller\ControllerResultInterface;
use Romchik38\Server\Models\DTO;

class ControllerResult extends DTO implements ControllerResultInterface
{

    public function __construct(
        string $response,
        array $path,
        string $type
    ) {
        $this->data[ControllerResultInterface::RESPONSE_FIELD] = $response;
        $this->data[ControllerResultInterface::PATH_FIELD] = $path;
        $this->data[ControllerResultInterface::TYPE_FIELD] = $type;
    }

    public function getResponse(): string
    {
        return $this->data[ControllerResultInterface::RESPONSE_FIELD];
    }

    public function getPath(): array
    {
        return $this->data[ControllerResultInterface::PATH_FIELD];
    }

    public function getType(): string
    {
        return $this->data[ControllerResultInterface::TYPE_FIELD];
    }
}
