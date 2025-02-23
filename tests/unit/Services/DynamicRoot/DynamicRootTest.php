<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTOFactory;
use Romchik38\Server\Services\Errors\EarlyAccessToCurrentRootErrorException;

class DynamicRootTest extends TestCase
{
    protected $DynamicRootDTOFactory;

    public function setUp(): void
    {
        $this->DynamicRootDTOFactory = $this->createMock(DynamicRootDTOFactory::class);
    }

    public function testGetDefaultRoot()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $this->DynamicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                new DynamicRootDTO('en'),
                new DynamicRootDTO('en'),
                new DynamicRootDTO('uk'),
            );

        $DynamicRoot = new DynamicRoot($default, $list, $this->DynamicRootDTOFactory);
        $defaultDTO = $DynamicRoot->getDefaultRoot();

        $this->assertSame('en', $defaultDTO->getName());
    }

    public function testGetList()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DynamicRootDTO('en');
        $secondDTO = new DynamicRootDTO('en');
        $thirdDTO = new DynamicRootDTO('uk');

        $listDTO = [$secondDTO, $thirdDTO];

        $this->DynamicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $DynamicRoot = new DynamicRoot($default, $list, $this->DynamicRootDTOFactory);
        $resultList = $DynamicRoot->getRootList();

        $this->assertSame($listDTO, $resultList);
    }

    public function testGetRootNames()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DynamicRootDTO('en');
        $secondDTO = new DynamicRootDTO('en');
        $thirdDTO = new DynamicRootDTO('uk');

        $this->DynamicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $DynamicRoot = new DynamicRoot($default, $list, $this->DynamicRootDTOFactory);
        $resultList = $DynamicRoot->getRootNames();

        $this->assertSame($list, $resultList);
    }

    /**
     *  getCurrentRoot return result
     *  also tested setCurrentRoot
     */
    public function testGetCurrentRootResult()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DynamicRootDTO('en');
        $secondDTO = new DynamicRootDTO('en');
        $thirdDTO = new DynamicRootDTO('uk');

        $this->DynamicRootDTOFactory->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $DynamicRoot = new DynamicRoot($default, $list, $this->DynamicRootDTOFactory);
        $DynamicRoot->setCurrentRoot('uk');
        $result = $DynamicRoot->getCurrentRoot()->getName();

        $this->assertSame('uk', $result);
    }

    /**
     *  getCurrentRoot throws error
     */
    public function testGetCurrentRootThrowsError()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DynamicRootDTO('en');
        $secondDTO = new DynamicRootDTO('en');
        $thirdDTO = new DynamicRootDTO('uk');

        $this->DynamicRootDTOFactory->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $this->expectException(EarlyAccessToCurrentRootErrorException::class);

        $DynamicRoot = new DynamicRoot($default, $list, $this->DynamicRootDTOFactory);
        $DynamicRoot->getCurrentRoot();
    }
}
