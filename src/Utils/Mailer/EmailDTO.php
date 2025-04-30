<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Mailer;

class EmailDTO implements EmailDTOInterface
{
    /** @param array<string,string> $headers */
    public function __construct(
        public readonly string $emailAddress,
        public readonly string $subject,
        public readonly string $message,
        public readonly array $headers
    ) {
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
