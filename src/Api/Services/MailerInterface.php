<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

use Romchik38\Server\Api\Models\DTO\Email\EmailDTOInterface;
use Romchik38\Server\Services\Errors\CantSendEmailException;

interface MailerInterface
{
    /**
     * Send email message to recipient
     *
     * @throws CantSendEmailException
     */
    public function send(EmailDTOInterface $emailDto): void;
}
