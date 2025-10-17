<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger\DeferredLogger;

use DateTime;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Romchik38\Server\Utils\Logger\AbstractLogger;
use Romchik38\Server\Utils\Mailer\CantSendEmailException;
use Romchik38\Server\Utils\Mailer\EmailDTOFactoryInterface;
use Romchik38\Server\Utils\Mailer\MailerInterface;

use function count;
use function implode;
use function phpversion;
use function sprintf;

class EmailLogger extends AbstractLogger implements DeferredLoggerInterface
{
    public function __construct(
        int $logLevel,
        protected MailerInterface $mailer,
        protected EmailDTOFactoryInterface $emailDtoFactory,
        protected string $recipient,
        protected string $sender,
        LoggerInterface|null $alternativeLogger = null,
    ) {
        parent::__construct($logLevel, $alternativeLogger);
    }

    public function write(string $level, string $message): void
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
        $dateString     = $date->format(self::DATE_TIME_FORMAT);
        foreach ($this->messages as $item) {
            [$level, $message] = $item;

            $messagesToSent[] = sprintf(
                '<p>Date: %s</p><p>Level: %s</p><p>Message: %s</p>',
                $dateString,
                $level,
                $message
            );
        }

        $subject = 'Log message';

        $headers = [
            'From'         => $this->sender,
            'Reply-To'     => $this->sender,
            'Content-type' => 'text/html',
            'X-Mailer'     => 'PHP/' . phpversion(),
        ];

        $emailDto = $this->emailDtoFactory->create(
            $this->recipient,
            $subject,
            implode("<br><br>", $messagesToSent),
            $headers
        );

        // send
        try {
            $this->mailer->send($emailDto);
        } catch (CantSendEmailException) {
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
