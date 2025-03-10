<?php

declare(strict_types=1);

namespace Romchik38\Server\Servers\Http;

use Exception;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Servers\Http\HttpServerInterface;
use Romchik38\Server\Api\Services\LoggerServerInterface;

use function header;
use function http_response_code;
use function implode;
use function sprintf;

class DefaultServer implements HttpServerInterface
{
    public function __construct(
        protected HttpRouterInterface $router,
        protected ControllerInterface $serverErrorController,
        protected LoggerServerInterface|null $logger = null
    ) {
    }

    public function log(): DefaultServer
    {
        if ($this->logger !== null) {
            $this->logger->sendAllLogs();
        }

        return $this;
    }

    public function run(): DefaultServer
    {
        try {
            $response   = $this->router->execute();
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
            // try to show the page
            try {
                $responseServerError = $this->serverErrorController
                    ->execute([$this::SERVER_ERROR_CONTROLLER_NAME]);

                $this->sendHeaders($responseServerError->getHeaders());
                echo (string) $responseServerError->getBody();
            } catch (Exception $e) {
                // log error from server error controller
                if ($this->logger) {
                    $this->logger->error($e->getMessage());
                }
                // show only a message
                echo $this::DEFAULT_SERVER_ERROR_MESSAGE;
            }
        }

        return $this;
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
