<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Email;

use Romchik38\Server\Models\DTO;
use Romchik38\Server\Api\Models\DTO\Email\EmailDTOInterface;

class EmailDTO extends DTO implements EmailDTOInterface
{

    public function __construct(
        string $emailAddress,
        string $subject,
        string $message,
        array $headers
    )
    {
        $this->data[$this::EMAIL] = $emailAddress;
        $this->data[$this::SUBJECT] = $subject;
        $this->data[$this::MESSAGE] = $message;
        $this->data[$this::HEADERS] = $headers;
    }

    public function getEmailAddress(): string {
        return $this->data[$this::EMAIL];
    }

    public function getSubject(): string {
        return $this->data[$this::SUBJECT];
    }

    public function getMessage(): string {
        return $this->data[$this::MESSAGE];
    }

    public function getHeaders(): array {
        return $this->data[$this::HEADERS];
    }
    
}