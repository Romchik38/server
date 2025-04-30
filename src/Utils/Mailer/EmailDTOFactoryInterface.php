<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Mailer;

interface EmailDTOFactoryInterface
{
    /** @param array<string,string> $headers */
    public function create(
        string $emailAddress,
        string $subject,
        string $message,
        array $headers
    ): EmailDTOInterface;
}
