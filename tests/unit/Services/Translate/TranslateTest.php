<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTOFactory;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTO;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModel;
use Romchik38\Server\Services\Translate\Translate;
use Romchik38\Server\Services\DymanicRoot\DymanicRoot;
use Romchik38\Server\Services\Errors\TranslateException;
use Romchik38\Server\Services\Translate\TranslateStorage;

class TranslateTest extends TestCase
{
    protected $dynamicRoot;
    protected $translateStorage;

    public function setUp(): void
    {
        $this->dynamicRoot = $this->createMock(DymanicRoot::class);
        $this->translateStorage = $this->createMock(TranslateStorage::class);
    }

    public function testT()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DymanicRoot(
            'en',
            ['en', 'uk'],
            new DymanicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot
        );

        $this->assertSame('якась фраза', $translate->t('some.key'));
    }

    public function testTthrowsErrorBecauseUnknownKey()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DymanicRoot(
            'en',
            ['en', 'uk'],
            new DymanicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot
        );

        $translate->t('unknown.key');
    }

    /**
     * We have a key on 'en' and 'uk'
     * Our default language is 'gb'
     * We must have phrase for key on 'gb' or will get an error
     * 
     * In other words:
     *   we set new default language, but do not make all translates for it in the database
     * 
     * Whant that translate works - load all phrases on the default language before change it
     */
    public function testTthrowsErrorBecauseNoDefaultVal()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DymanicRoot(
            'gb',
            ['en', 'uk'],
            new DymanicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot
        );

        $translate->t('some.key');
    }


    protected function createHash()
    {
        $key = 'some.key';

        $dto = new TranslateEntityDTO('some.key', [
            'en' => 'phrase some',
            'uk' => 'якась фраза'
        ]);

        return [$key => $dto];
    }
}
