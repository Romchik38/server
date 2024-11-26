<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Errors;

/**
 * @api
 * 
 * Used inside Action execute method.
 * It means - a route was provided, but dynamic action does not know it
 * The Controller must catch it and deside what to do next
 * 
 */
class ActionNotFoundException extends \RuntimeException {}
