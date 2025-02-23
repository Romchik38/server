<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mailer;

use Romchik38\Server\Api\Models\DTO\Email\EmailDTOInterface;
use Romchik38\Server\Api\Services\MailerInterface;
use Romchik38\Server\Services\Errors\CantSendEmailException;

use function mail;

class PhpMail implements MailerInterface
{
    /**
     * Send an email
     *
     * @throws CantSendEmailException [if result is false]
     */
    public function send(EmailDTOInterface $emailDTO): void
    {
        $result = mail(
            $emailDTO->getEmailAddress(),
            $emailDTO->getSubject(),
            $emailDTO->getMessage(),
            $emailDTO->getHeaders()
        );

        if ($result === false) {
            throw new CantSendEmailException('PhpMail. Email was not sent (result is false)');
        }
    }
}
