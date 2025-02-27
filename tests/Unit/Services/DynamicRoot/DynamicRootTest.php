<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\DynamicRoot;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDtoFactory;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\DynamicRoot\EarlyAccessToCurrentRootErrorException;

final class DynamicRootTest extends TestCase
{
    public function testGetDefaultRoot()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $dynamicRootDtoFactory = $this->createMock(DynamicRootDtoFactory::class);

        $dynamicRootDtoFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                new DynamicRootDTO('en'),
                new DynamicRootDTO('en'),
                new DynamicRootDTO('uk'),
            );

        $dynamicRoot = new DynamicRoot($default, $list, $dynamicRootDtoFactory);
        $defaultDto  = $dynamicRoot->getDefaultRoot();

        $this->assertSame('en', $defaultDto->getName());
    }

    public function testGetList()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $firstDto  = new DynamicRootDTO('en');
        $secondDto = new DynamicRootDTO('en');
        $thirdDto  = new DynamicRootDTO('uk');

        $listDto = [$secondDto, $thirdDto];

        $dynamicRootDtoFactory = $this->createMock(DynamicRootDtoFactory::class);

        $dynamicRootDtoFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDto,
                $secondDto,
                $thirdDto,
            );

        $dynamicRoot = new DynamicRoot($default, $list, $dynamicRootDtoFactory);
        $resultList  = $dynamicRoot->getRootList();

        $this->assertSame($listDto, $resultList);
    }

    public function testGetRootNames()
    {
        $default = 'en';
        $list    = ['en', 'uk'];

        $firstDto  = new DynamicRootDTO('en');
        $secondDto = new DynamicRootDTO('en');
        $thirdDto  = new DynamicRootDTO('uk');

        $dynamicRootDtoFactory = $this->createMock(DynamicRootDtoFactory::class);

        $dynamicRootDtoFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDto,
                $secondDto,
                $thirdDto,
            );

        $dynamicRoot = new DynamicRoot($default, $list, $dynamicRootDtoFactory);
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

        $firstDto  = new DynamicRootDTO('en');
        $secondDto = new DynamicRootDTO('en');
        $thirdDto  = new DynamicRootDTO('uk');

        $dynamicRootDtoFactory = $this->createMock(DynamicRootDtoFactory::class);

        $dynamicRootDtoFactory->method('create')
            ->willReturn(
                $firstDto,
                $secondDto,
                $thirdDto,
            );

        $dynamicRoot = new DynamicRoot($default, $list, $dynamicRootDtoFactory);
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

        $firstDto  = new DynamicRootDTO('en');
        $secondDto = new DynamicRootDTO('en');
        $thirdDto  = new DynamicRootDTO('uk');

        $dynamicRootDtoFactory = $this->createMock(DynamicRootDtoFactory::class);

        $dynamicRootDtoFactory->method('create')
            ->willReturn(
                $firstDto,
                $secondDto,
                $thirdDto,
            );

        $this->expectException(EarlyAccessToCurrentRootErrorException::class);

        $dynamicRoot = new DynamicRoot($default, $list, $dynamicRootDtoFactory);
        $dynamicRoot->getCurrentRoot();
    }
}
