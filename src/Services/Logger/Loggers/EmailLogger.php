<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Logger\Loggers;

use DateTime;
use Psr\Log\LogLevel;
use Romchik38\Server\Api\Models\DTO\Email\EmailDTOFactoryInterface;
use Romchik38\Server\Api\Services\LoggerServerInterface;
use Romchik38\Server\Api\Services\MailerInterface;
use Romchik38\Server\Services\Errors\CantSendEmailException;
use Romchik38\Server\Services\Logger\Logger;

use function count;
use function implode;
use function phpversion;

class EmailLogger extends Logger
{
    public function __construct(
        int $logLevel,
        protected MailerInterface $mailer,
        protected EmailDTOFactoryInterface $emailDTOFactory,
        protected string $recipient,
        protected string $sender,
        protected LoggerServerInterface|null $alternativeLogger = null,
    ) {
        parent::__construct($logLevel, $alternativeLogger);
    }

    public function write(string $level, string $message)
    {
        $this->messages[] = [$level, $message];
    }

    public function sendAllLogs(): void
    {
        if (count($this->messages) === 0) {
            return;
        }

        // write
        $writeErrors    = [];
        $messagesToSent = [];
        $date           = new DateTime();
        $dateString     = $date->format(LoggerServerInterface::DATE_TIME_FORMAT);
        foreach ($this->messages as $item) {
            [$level, $message] = $item;

            $messagesToSent[] = '<p>Date: ' . $dateString . '</p><p>Level: '
                . $level . '</p><p>Message: ' . $message . '</p>';
        }

        $subject = 'Log message';

        $headers = [
            'From'         => $this->sender,
            'Reply-To'     => $this->sender,
            'Content-type' => 'text/html',
            'X-Mailer'     => 'PHP/' . phpversion(),
        ];

        $emailDTO = $this->emailDTOFactory->create(
            $this->recipient,
            $subject,
            implode("<br><br>", $messagesToSent),
            $headers
        );

        // send
        try {
            $this->mailer->send($emailDTO);
        } catch (CantSendEmailException $e) {
            $writeErrors[] = $item;
        }

        if (count($writeErrors) > 0) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, self::class . ' - some logs did not send');
                $this->sendAllToalternativeLog($writeErrors);
            }
        }
    }
}
