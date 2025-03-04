<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Translate;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTO;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\Logger\Loggers\FileLogger;
use Romchik38\Server\Services\Translate\Translate;
use Romchik38\Server\Services\Translate\TranslateException;
use Romchik38\Server\Services\Translate\TranslateStorage;

class TranslateTest extends TestCase
{
    public function testT()
    {
        $dynamicRoot      = $this->createMock(DynamicRoot::class);
        $translateStorage = $this->createMock(TranslateStorage::class);

        $translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash('some.key'));

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $translate = new Translate(
            $translateStorage,
            $dynamicRoot
        );

        $this->assertSame('якась фраза', $translate->t('some.key'));
    }

    public function testThrowsErrorBecauseUnknownKey()
    {
        $dynamicRoot      = $this->createMock(DynamicRoot::class);
        $translateStorage = $this->createMock(TranslateStorage::class);

        $translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash('some.key'));

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);
        $this->expectExceptionMessage(
            'Translation for string unknown.key is missing. Please create it for default en language first'
        );

        $translate = new Translate(
            $translateStorage,
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
        $dynamicRoot      = $this->createMock(DynamicRoot::class);
        $translateStorage = $this->createMock(TranslateStorage::class);

        $translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash('some.key'));

        $dynamicRoot = new DynamicRoot('gb', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $this->expectException(TranslateException::class);
        $this->expectExceptionMessage('Default value for language gb isn\'t set');

        $translate = new Translate(
            $translateStorage,
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
        $dynamicRoot      = $this->createMock(DynamicRoot::class);
        $translateStorage = $this->createMock(TranslateStorage::class);

        $translateStorage->method('getDataByLanguages')
            ->willReturn($this->createHash('some.key'));

        $dynamicRoot = new DynamicRoot('en', ['en', 'gb']);
        $dynamicRoot->setCurrentRoot('gb');

        $logger = $this->createMock(FileLogger::class);

        $logMessage = Translate::class . ': Missed translation for key some.key language gb';
        $logger->expects($this->once())->method('log')->with(LogLevel::DEBUG, $logMessage);

        $translate = new Translate(
            $translateStorage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('phrase some', $translate->t('some.key'));
    }

    public function testTranslateWithSpecificLanguage()
    {
        $dynamicRoot      = $this->createMock(DynamicRoot::class);
        $translateStorage = $this->createMock(TranslateStorage::class);

        $key              = 'some.key';
        $specificLanguage = 'uk';
        $translateStorage->method('getDataByLanguages')
            ->willReturn([
                $key => new TranslateEntityDTO(
                    $key,
                    [
                        'en' => 'phrase some',
                    ]
                ),
            ]);

        $translateStorage
            ->expects($this->once())
            ->method('getAllDataByKey')
            ->with($key)
            ->willReturn($this->createHash($key));

        $dynamicRoot = new DynamicRoot('en', ['en']);

        $dynamicRoot->setCurrentRoot('en');

        $translate = new Translate(
            $translateStorage,
            $dynamicRoot
        );

        $res = $translate->translate($key, $specificLanguage);
        $this->assertSame('якась фраза', $res);
    }

    /** @return array<string,TranslateEntityDTO> */
    protected function createHash(string $key): array
    {
        $dto = new TranslateEntityDTO($key, [
            'en' => 'phrase some',
            'uk' => 'якась фраза',
        ]);

        return [$key => $dto];
    }
}
