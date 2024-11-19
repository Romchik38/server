<?php

namespace Romchik38\Server\Models\Errors;

/** 
 * Use when state of the database is incorrect. 
 * For example expect 1 entity but get more
 */
class RepositoryConsistencyException extends \RuntimeException {}
