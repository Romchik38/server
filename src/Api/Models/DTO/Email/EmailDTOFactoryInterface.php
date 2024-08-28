<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Email;

use Romchik38\Server\Api\Models\DTO\Email\EmailDTOInterface;

interface EmailDTOFactoryInterface {

    public function create(
        string $emailAddress,
        string $subject,
        string $message,
        array $headers
    ): EmailDTOInterface;
}