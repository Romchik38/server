<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\TranslateEntity\Sql;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\TranslateEntity\Sql\TranslateEntityModelRepository;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModelFactory;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModel;

class TranslateEntityModelRepositoryTest extends TestCase
{
    protected $database;
    protected $factory;
    protected $repository;
    protected $table = 'table';

    public function setUp(): void {
        $this->database = $this->createMock(DatabasePostgresql::class);
        $this->factory = $this->createMock(TranslateEntityModelFactory::class);
        $this->repository = new TranslateEntityModelRepository(
            $this->database, $this->factory, $this->table, TranslateEntityModel::ID_FIELD
        );
    }

    public function testGetListByLanguages()
    {
        $id1 = 1;
        $key1 = 'some.key1';
        $language1 = 'en';
        $phrase1 = 'some phrase1';

        $id2 = 2;
        $key2 = 'some.key2';
        $language2 = 'uk';
        $phrase2 = 'some phrase2';

        $languages = [$language1, $language2];

        $listQueryPart = 'SELECT table.* FROM table WHERE';
        $query = $listQueryPart. ' ' . TranslateEntityModel::LANGUAGE_FIELD . ' = $1 OR '
            . TranslateEntityModel::LANGUAGE_FIELD . ' = $2';
        $databaseResult1 = [
            TranslateEntityModel::ID_FIELD => $id1,
            TranslateEntityModel::LANGUAGE_FIELD => $language1,
            TranslateEntityModel::KEY_FIELD => $key1,
            TranslateEntityModel::PHRASE_FIELD => $phrase1
        ];
        $databaseResult2 = [
            TranslateEntityModel::ID_FIELD => $id2,
            TranslateEntityModel::LANGUAGE_FIELD => $language2,
            TranslateEntityModel::KEY_FIELD => $key2,
            TranslateEntityModel::PHRASE_FIELD => $phrase2
        ];

        $this->factory->expects($this->exactly(2))->method('create')
            ->willReturn(new TranslateEntityModel(), new TranslateEntityModel());

        $this->database->expects($this->once())->method('queryParams')
            ->with($query, $languages)->willReturn([$databaseResult1, $databaseResult2]);

        [$result1, $result2] = $this->repository->getListByLanguages($languages);

        $this->assertEquals($id1, $result1->getId());
        $this->assertEquals($key1, $result1->getKey());
        $this->assertEquals($language1, $result1->getLanguage());
        $this->assertEquals($phrase1, $result1->getPhrase());

        $this->assertEquals($id2, $result2->getId());
        $this->assertEquals($key2, $result2->getKey());
        $this->assertEquals($language2, $result2->getLanguage());
        $this->assertEquals($phrase2, $result2->getPhrase());
    }

    public function testGetByKey()
    {
        $key = 'some.key';

        $id1 = 1;
        $language1 = 'en';
        $phrase1 = 'some phrase';

        $id2 = 2;
        $language2 = 'uk';
        $phrase2 = 'якасть фраза';

        $databaseQueryPart = 'SELECT table.* FROM table ';
        $listQueryPart = sprintf(
            'WHERE %s.key = $1', 
            $this->table
        );
        $query = $databaseQueryPart . $listQueryPart;

        $databaseResult1 = [
            TranslateEntityModel::ID_FIELD => $id1,
            TranslateEntityModel::LANGUAGE_FIELD => $language1,
            TranslateEntityModel::KEY_FIELD => $key,
            TranslateEntityModel::PHRASE_FIELD => $phrase1
        ];
        $databaseResult2 = [
            TranslateEntityModel::ID_FIELD => $id2,
            TranslateEntityModel::LANGUAGE_FIELD => $language2,
            TranslateEntityModel::KEY_FIELD => $key,
            TranslateEntityModel::PHRASE_FIELD => $phrase2
        ];

        $this->factory->expects($this->exactly(2))->method('create')
            ->willReturn(new TranslateEntityModel(), new TranslateEntityModel());

        $this->database->expects($this->once())->method('queryParams')
            ->with($query, [$key])->willReturn([$databaseResult1, $databaseResult2]);

        [$result1, $result2] = $this->repository->getByKey($key);

        $this->assertEquals($id1, $result1->getId());
        $this->assertEquals($key, $result1->getKey());
        $this->assertEquals($language1, $result1->getLanguage());
        $this->assertEquals($phrase1, $result1->getPhrase());

        $this->assertEquals($id2, $result2->getId());
        $this->assertEquals($key, $result2->getKey());
        $this->assertEquals($language2, $result2->getLanguage());
        $this->assertEquals($phrase2, $result2->getPhrase());
    }
}
