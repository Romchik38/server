<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTOFactory;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTO;
use Romchik38\Server\Services\Translate\Translate;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\Errors\TranslateException;
use Romchik38\Server\Services\Translate\TranslateStorage;
use Romchik38\Server\Services\Logger\Loggers\FileLogger;

class TranslateTest extends TestCase
{
    protected $dynamicRoot;
    protected $translateStorage;

    public function setUp(): void
    {
        $this->dynamicRoot = $this->createMock(DynamicRoot::class);
        $this->translateStorage = $this->createMock(TranslateStorage::class);
    }

    public function testT()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DynamicRoot(
            'en',
            ['en', 'uk'],
            new DynamicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot
        );

        $this->assertSame('якась фраза', $translate->t('some.key'));
    }

    public function testThrowsErrorBecauseUnknownKey()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DynamicRoot(
            'en',
            ['en', 'uk'],
            new DynamicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);
        $this->expectExceptionMessage('Translation for string unknown.key is missing. Please create it for default en language first');

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

        $dynamicRoot = new DynamicRoot(
            'gb',
            ['en', 'uk'],
            new DynamicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);
        $this->expectExceptionMessage('Default value for language gb isn\'t set');

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot
        );

        $translate->t('some.key');
    }

    /**
     * Missin translation for given key and language
     * In that case function makes log and returns phrase default language
     * 
     * test:
     *    - log
     *    - default phrase
     */
    public function testTranslateWithLoggerNoTranslation()
    {
        $this->translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash());

        $dynamicRoot = new DynamicRoot(
            'en',
            ['en', 'gb'],
            new DynamicRootDTOFactory
        );
        $dynamicRoot->setCurrentRoot('gb');

        $logger = $this->createMock(FileLogger::class);

        $logMessage = Translate::class . ': Missed translation for key some.key language gb';
        $logger->expects($this->once())->method('log')->with(LogLevel::DEBUG, $logMessage);

        $translate = new Translate(
            $this->translateStorage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('phrase some', $translate->t('some.key'));
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
