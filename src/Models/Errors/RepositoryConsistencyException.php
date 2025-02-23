<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Errors;

use RuntimeException;

/**
 * Use when state of the database is incorrect.
 * For example expect 1 entity but get more
 */
class RepositoryConsistencyException extends RuntimeException
{
}
