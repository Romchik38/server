<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mailer;

use Romchik38\Server\Api\Models\DTO\Email\EmailDTOInterface;
use Romchik38\Server\Api\Services\MailerInterface;

use function mail;

class PhpMail implements MailerInterface
{
    /**
     * Send an email
     *
     * @throws CantSendEmailException - If result is false.
     */
    public function send(EmailDTOInterface $emailDto): void
    {
        $result = mail(
            $emailDto->getEmailAddress(),
            $emailDto->getSubject(),
            $emailDto->getMessage(),
            $emailDto->getHeaders()
        );

        if ($result === false) {
            throw new CantSendEmailException('PhpMail. Email was not sent (result is false)');
        }
    }
}
