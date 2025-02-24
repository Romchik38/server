<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Translate;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\TranslateEntity\TranslateEntityDTOFactory;
use Romchik38\Server\Models\TranslateEntity\Sql\TranslateEntityModelRepository;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModel;
use Romchik38\Server\Services\Translate\TranslateStorage;

use function array_key_exists;

final class TranslateStorageTest extends TestCase
{
    public function testGetDataByLanguages()
    {
        $repostory = $this->createMock(TranslateEntityModelRepository::class);

        $languages = ['en', 'uk'];

        $key = 'key.about';

        $model1 = new TranslateEntityModel();
        $model1->setKey($key);
        $model1->setLanguage('en');
        $model1->setPhrase('phrase1');

        $model2 = new TranslateEntityModel();
        $model2->setKey($key);
        $model2->setLanguage('uk');
        $model2->setPhrase('фраза1');

        $repostory->expects($this->once())->method('getListByLanguages')
            ->with($languages)->willReturn([$model1, $model2]);

        $translateStorage = new TranslateStorage(
            $repostory,
            new TranslateEntityDTOFactory()
        );

        $hash = $translateStorage->getDataByLanguages($languages);

        $this->assertSame(true, array_key_exists($key, $hash));

        $dto = $hash[$key];

        $this->assertSame('phrase1', $dto->getPhrase('en'));
        $this->assertSame('фраза1', $dto->getPhrase('uk'));
    }

    public function testGetAllDataByKey()
    {
        $repostory = $this->createMock(TranslateEntityModelRepository::class);

        $key = 'key.about';

        $model1 = new TranslateEntityModel();
        $model1->setKey($key);
        $model1->setLanguage('en');
        $model1->setPhrase('phrase1');

        $model2 = new TranslateEntityModel();
        $model2->setKey($key);
        $model2->setLanguage('uk');
        $model2->setPhrase('фраза1');

        $repostory->expects($this->once())->method('getByKey')
            ->with($key)->willReturn([$model1, $model2]);

        $translateStorage = new TranslateStorage(
            $repostory,
            new TranslateEntityDTOFactory()
        );

        $hash = $translateStorage->getAllDataByKey($key);

        $this->assertSame(true, array_key_exists($key, $hash));

        $dto = $hash[$key];

        $this->assertSame('phrase1', $dto->getPhrase('en'));
        $this->assertSame('фраза1', $dto->getPhrase('uk'));
    }
}
