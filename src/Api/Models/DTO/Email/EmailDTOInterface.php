<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Email;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface EmailDTOInterface extends DTOInterface
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
