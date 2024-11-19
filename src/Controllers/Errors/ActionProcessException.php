<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Errors;

/**
 * @api
 * 
 * Used in any Action to stop execution and show server errror page
 */
class ActionProcessException extends \RuntimeException {}
