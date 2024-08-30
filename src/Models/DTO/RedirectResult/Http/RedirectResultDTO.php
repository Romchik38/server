<?php

namespace Romchik38\Server\Models\DTO\RedirectResult\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Models\DTO;

class RedirectResultDTO extends DTO implements RedirectResultDTOInterface
{

    public function __construct(
        string $uri,
        int $statusCode
    ) {
        $this->data[RedirectResultDTOInterface::REDIRECT_LOCATION_FIELD] = $uri;
        $this->data[RedirectResultDTOInterface::REDIRECT_STATUS_CODE_FIELD] = $statusCode;
    }

    public function getRedirectLocation(): string
    {
        return $this->getData(RedirectResultDTOInterface::REDIRECT_LOCATION_FIELD);
    }

    public function getStatusCode(): int
    {
        return $this->getData(RedirectResultDTOInterface::REDIRECT_STATUS_CODE_FIELD);
    }
}
