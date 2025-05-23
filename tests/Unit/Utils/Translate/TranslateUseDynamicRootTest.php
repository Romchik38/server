<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Translate;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;
use Romchik38\Server\Tests\Unit\Utils\Translate\Samples\Logger;
use Romchik38\Server\Tests\Unit\Utils\Translate\Samples\Storage;
use Romchik38\Server\Tests\Unit\Utils\Translate\Samples\Storage2;
use Romchik38\Server\Utils\Translate\TranslateException;
use Romchik38\Server\Utils\Translate\TranslateUseDynamicRoot;

class TranslateUseDynamicRootTest extends TestCase
{
    public function testT()
    {
        $data = include __DIR__ . '/Samples/data.php';

        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot
        );

        $this->assertSame('Опис ключа 1', $translate->t('key1'));
    }

    public function testTthrowsError(): void
    {
        $data = include __DIR__ . '/Samples/data.php';

        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);

        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot
        );

        $this->expectException(TranslateException::class);
        $this->assertSame('Опис ключа 1', $translate->t('key1'));
    }

    public function testTranslateFindRequestedTranslate(): void
    {
        $data = include __DIR__ . '/Samples/data.php';

        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot
        );

        $this->assertSame('Опис ключа 1', $translate->translate('key1', 'uk'));
    }

    /** With logging */
    public function testTranslateReturnsTranslateForDefaultLanguage(): void
    {
        $data = include __DIR__ . '/Samples/data.php';

        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $logger    = new Logger();
        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('Description key2', $translate->translate('key2', 'uk'));
        $this->assertSame('debug', $logger->level);
        $this->assertSame('Translate key key2 does not have translate in uk language', $logger->message);
    }

    /** With logging */
    public function testTranslateReturnsKeyBecauseOfItDoesNotExist(): void
    {
        $data = include __DIR__ . '/Samples/data.php';

        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $logger    = new Logger();
        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('key3', $translate->translate('key3', 'uk'));
        $this->assertSame('debug', $logger->level);
        $this->assertSame('Translate key key3 does not exist', $logger->message);
    }

    /** With logging */
    public function testTranslateReturnsKeyBecauseOfMissingTranslates(): void
    {
        $data    = include __DIR__ . '/Samples/data.php';
        $storage = new Storage($data);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $logger = new Logger();

        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('key4', $translate->translate('key4', 'uk'));
        $this->assertSame('debug', $logger->level);
        $this->assertSame('Translate key key4 does not have translate in en default language', $logger->message);
    }

    /** With logging */
    public function testStorageThrowsError(): void
    {
        $storage = new Storage2([]);

        $dynamicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynamicRoot->setCurrentRoot('uk');

        $logger = new Logger();

        $translate = new TranslateUseDynamicRoot(
            $storage,
            $dynamicRoot,
            $logger
        );

        $this->assertSame('key', $translate->t('key'));
        $this->assertSame(
            'Error while getting a translate key from storage: Database is down',
            $logger->message
        );
    }
}
