<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Email;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface EmailDTOInterface extends DTOInterface {

    const EMAIL = 'email';
    const SUBJECT = 'subject';
    const MESSAGE = 'message';
    const HEADERS = 'headers';

    public function getEmailAddress(): string;
    public function getSubject(): string;
    public function getMessage(): string;
    public function getHeaders(): array;

}