<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Servers;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function header;
use function http_response_code;
use function implode;
use function sprintf;

class DefaultServer implements HttpServerInterface
{
    public function __construct(
        protected RequestHandlerInterface $router,
        protected RequestHandlerInterface $serverErrorController,
        protected LoggerInterface|null $logger = null
    ) {
    }

    public function handle(ServerRequestInterface $request): void
    {
        try {
            $response   = $this->router->handle($request);
            $headers    = $response->getHeaders();
            $statusCode = $response->getStatusCode();

            $this->sendHeaders($headers);

            if ($statusCode > 0) {
                http_response_code($statusCode);
            }

            echo (string) $response->getBody();
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error($e->getMessage());
            }
            http_response_code($this::DEFAULT_SERVER_ERROR_CODE);
            // try to handle the error
            try {
                $errorRequest        = $request->withAttribute(self::REQUEST_ERROR_ATTRIBUTE_NAME, $e);
                $responseServerError = $this->serverErrorController->handle($errorRequest);
                $this->sendHeaders($responseServerError->getHeaders());
                echo (string) $responseServerError->getBody();
            } catch (Exception $e) {
                // log error from server error controller
                if ($this->logger) {
                    $this->logger->error('Server error controller throws an error: ' . $e->getMessage());
                }
                // show only a message
                echo $this::DEFAULT_SERVER_ERROR_MESSAGE;
            }
        }
    }

    /** @param array<string, array<int, string>> $headers */
    private function sendHeaders(array $headers): void
    {
        foreach ($headers as $key => $value) {
            $line = sprintf(
                '%s: %s',
                $key,
                implode(',', $value)
            );
            header($line);
        }
    }
}
