<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTO;
use Romchik38\Server\Services\DymanicRoot\DymanicRoot;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTOFactory;
use Romchik38\Server\Services\Errors\EarlyAccessToCurrentRootError;

class DymanicRootTest extends TestCase
{
    protected $dymanicRootDTOFactory;

    public function setUp(): void
    {
        $this->dymanicRootDTOFactory = $this->createMock(DymanicRootDTOFactory::class);
    }

    public function testGetDefaultRoot()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $this->dymanicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                new DymanicRootDTO('en'),
                new DymanicRootDTO('en'),
                new DymanicRootDTO('uk'),
            );

        $dymanicRoot = new DymanicRoot($default, $list, $this->dymanicRootDTOFactory);
        $defaultDTO = $dymanicRoot->getDefaultRoot();

        $this->assertSame('en', $defaultDTO->getName());
    }

    public function testGetList()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DymanicRootDTO('en');
        $secondDTO = new DymanicRootDTO('en');
        $thirdDTO = new DymanicRootDTO('uk');

        $listDTO = [$secondDTO, $thirdDTO];

        $this->dymanicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $dymanicRoot = new DymanicRoot($default, $list, $this->dymanicRootDTOFactory);
        $resultList = $dymanicRoot->getRootList();

        $this->assertSame($listDTO, $resultList);
    }

    public function testGetRootNames()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DymanicRootDTO('en');
        $secondDTO = new DymanicRootDTO('en');
        $thirdDTO = new DymanicRootDTO('uk');

        $this->dymanicRootDTOFactory->expects($this->exactly(3))->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $dymanicRoot = new DymanicRoot($default, $list, $this->dymanicRootDTOFactory);
        $resultList = $dymanicRoot->getRootNames();

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

        $firstDTO = new DymanicRootDTO('en');
        $secondDTO = new DymanicRootDTO('en');
        $thirdDTO = new DymanicRootDTO('uk');

        $this->dymanicRootDTOFactory->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $dymanicRoot = new DymanicRoot($default, $list, $this->dymanicRootDTOFactory);
        $dymanicRoot->setCurrentRoot('uk');
        $result = $dymanicRoot->getCurrentRoot()->getName();

        $this->assertSame('uk', $result);
    }

    /**
     *  getCurrentRoot throws error
     */
    public function testGetCurrentRootThrowsError()
    {
        $default = 'en';
        $list = ['en', 'uk'];

        $firstDTO = new DymanicRootDTO('en');
        $secondDTO = new DymanicRootDTO('en');
        $thirdDTO = new DymanicRootDTO('uk');

        $this->dymanicRootDTOFactory->method('create')
            ->willReturn(
                $firstDTO,
                $secondDTO,
                $thirdDTO,
            );

        $this->expectException(EarlyAccessToCurrentRootError::class);

        $dymanicRoot = new DymanicRoot($default, $list, $this->dymanicRootDTOFactory);
        $dymanicRoot->getCurrentRoot();
    }
}
