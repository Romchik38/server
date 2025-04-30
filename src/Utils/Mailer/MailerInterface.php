<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Mailer;

interface MailerInterface
{
    /**
     * Send email message to recipient
     *
     * @throws CantSendEmailException
     */
    public function send(EmailDTOInterface $emailDto): void;
}
