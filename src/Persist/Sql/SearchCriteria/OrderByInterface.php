<?php

declare(strict_types=1);

namespace Romchik38\Server\Persist\Sql\SearchCriteria;

interface OrderByInterface
{
    public const ASC_DIRECTION      = 'ASC';
    public const DESC_DIRECTION     = 'DESC';
    public const NULLS_FIRST_OPTION = 'NULLS FIRST';
    public const NULLS_LAST_OPTION  = 'NULLS LAST';

    public function getField(): string;

    public function getDirection(): string;

    public function getNulls(): string;
}
