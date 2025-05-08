<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Persist\Sql\SearchCriteria;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Persist\Sql\SearchCriteria\OrderBy;

final class OrderByTest extends TestCase
{
    /**
     * Tested:
     *   - getField
     *   - getDirection
     *   - getNulls
     */
    public function testGets(): void
    {
        $orderBy = new OrderBy('field1', 'asc', 'Nulls First');
        $this->assertSame('field1', $orderBy->getField());
        $this->assertSame('asc', $orderBy->getDirection());
        $this->assertSame('Nulls First', $orderBy->getNulls());
    }

    public function testGetDirectionAndNullsWithDefault(): void
    {
        $orderBy = new OrderBy('field1');
        $this->assertSame('field1', $orderBy->getField());
        $this->assertSame(OrderBy::ASC_DIRECTION, $orderBy->getDirection());
        $this->assertSame(OrderBy::NULLS_LAST_OPTION, $orderBy->getNulls());
    }
}
