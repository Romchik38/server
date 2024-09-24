<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Api;

use Romchik38\Server\Api\Models\DTO\Api\ApiDTOInterface;
use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;
use Romchik38\Server\Models\DTO\DefaultView\DefaultViewDTO;

class ApiDTO extends DefaultViewDTO implements ApiDTOInterface
{
    public function __construct(
        string $name,
        string $description,
        string $status,
        mixed $result
    ) {
        $this->data[DefaultViewDTOInterface::DEFAULT_NAME_FIELD] = $name;
        $this->data[DefaultViewDTOInterface::DEFAULT_DESCRIPTION_FIELD] = $description;
        $this->data[$this::STATUS_FIELD] = $status;
        $this->data[$this::RESULT_FIELD] = $result;
    }

    public function getStatus(): string
    {
        return $this->data[$this::STATUS_FIELD];
    }

    public function getResult(): mixed
    {
        return $this->data[$this::RESULT_FIELD];
    }
}
