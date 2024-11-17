<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Errors;

/** Used when a route is expected, but not found
 * can be used in DynamicAction getDescription() method
 */
class DynamicActionLogicException extends \RuntimeException {}
