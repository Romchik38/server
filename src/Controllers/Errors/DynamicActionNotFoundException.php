<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Errors;

/**
 * A route was provided, but dynamic action does not know it
 */
class DynamicActionNotFoundException extends \RuntimeException {

}