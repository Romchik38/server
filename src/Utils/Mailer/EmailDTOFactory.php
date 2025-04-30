<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Mailer;

class EmailDTOFactory implements EmailDTOFactoryInterface
{
    public function create(
        string $emailAddress,
        string $subject,
        string $message,
        array $headers
    ): EmailDTOInterface {
        return new EmailDTO(
            $emailAddress,
            $subject,
            $message,
            $headers
        );
    }
}
