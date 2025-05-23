<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto\Api;

use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;

class ApiDTO extends DefaultViewDTO implements ApiDTOInterface
{
    public function __construct(
        string $name,
        string $description,
        protected readonly string $status,
        protected readonly mixed $result
    ) {
        parent::__construct($name, $description);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function jsonSerialize(): mixed
    {
        return [
            self::DEFAULT_NAME_FIELD        => $this->name,
            self::DEFAULT_DESCRIPTION_FIELD => $this->description,
            self::STATUS_FIELD              => $this->status,
            self::RESULT_FIELD              => $this->result,
        ];
    }
}
