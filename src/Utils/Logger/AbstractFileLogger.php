<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Romchik38\Server\Utils\Logger\AbstractLogger;

use function fclose;
use function file_exists;
use function fopen;
use function sprintf;

abstract class AbstractFileLogger extends AbstractLogger
{
    protected readonly string $fullFilePath;

    /**
     * @param string $protocol [ file:// (default), http://, ftp:// etc.]
     * @param resource $context — [optional]
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $fileName,
        int $logLevel,
        string $protocol = 'file://',
        protected readonly bool $useIncludePath = false,
        protected $context = null,
        protected LoggerInterface|null $alternativeLogger = null
    ) {
        parent::__construct($logLevel, $alternativeLogger);
        $this->fullFilePath = $protocol . $fileName;
        if (! file_exists($this->fullFilePath)) {
            $fp = fopen($this->fullFilePath, 'a', $this->useIncludePath, $this->context);
            if ($fp === false) {
                throw new InvalidArgumentException(sprintf(
                    'Colud not create file %s',
                    $this->fullFilePath
                ));
            } else {
                fclose($fp);
            }
        }
    }
}
