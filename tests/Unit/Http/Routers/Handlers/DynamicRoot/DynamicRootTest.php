<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Handlers\DynamicRoot;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\EarlyAccessToCurrentRootErrorException;

final class DynamicRootTest extends TestCase
{
    public function testGetDefaultRoot()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $dynamicRoot = new DynamicRoot($default, $list);
        $defaultDto  = $dynamicRoot->getDefaultRoot();

        $this->assertSame('en', $defaultDto->getName());
    }

    public function testGetList()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $dynamicRoot = new DynamicRoot($default, $list);
        $resultList  = $dynamicRoot->getRootList();
        $dto1        = $resultList[0];
        $dto2        = $resultList[1];

        $this->assertSame('en', $dto1->getName());
        $this->assertSame('uk', $dto2->getName());
    }

    public function testGetRootNames()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $dynamicRoot = new DynamicRoot($default, $list);
        $resultList  = $dynamicRoot->getRootNames();

        $this->assertSame($list, $resultList);
    }

    /**
     *  getCurrentRoot return result
     *  also tested setCurrentRoot
     */
    public function testGetCurrentRootResult()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $dynamicRoot = new DynamicRoot($default, $list);
        $dynamicRoot->setCurrentRoot('uk');
        $result = $dynamicRoot->getCurrentRoot()->getName();

        $this->assertSame('uk', $result);
    }

    /**
     *  getCurrentRoot throws error
     */
    public function testGetCurrentRootThrowsError()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $this->expectException(EarlyAccessToCurrentRootErrorException::class);

        $dynamicRoot = new DynamicRoot($default, $list);
        $dynamicRoot->getCurrentRoot();
    }
}
