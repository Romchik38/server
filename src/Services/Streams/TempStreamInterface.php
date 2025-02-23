<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Streams;

/** Accepts data from function as a resource and converts it to a string */
interface TempStreamInterface
{
    /**
     * @param callable $fn Function to call
     * @param int $resourceIndex indext in param $args to insert resource
     * @param array<int,mixed> $args argements to pass into callback with index for recource
     * @throws StreamProcessException If callable returns false ot throws an exception.
     */
    public function writeFromCallable(callable $fn, int $resourceIndex, ...$args): void;

    /**
     * @throws StreamProcessException On write error.
     */
    public function write(string $data): void;

    /**
     * Closes the stream and returns all data as a string
     *
     * @throws StreamProcessException On further calls.
     * */
    public function __invoke(): string;
}
