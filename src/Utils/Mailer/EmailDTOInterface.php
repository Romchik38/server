<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Mailer;

interface EmailDTOInterface
{
    public const EMAIL   = 'email';
    public const SUBJECT = 'subject';
    public const MESSAGE = 'message';
    public const HEADERS = 'headers';

    public function getEmailAddress(): string;

    public function getSubject(): string;

    public function getMessage(): string;

    /** @return array<string,string> */
    public function getHeaders(): array;
}
