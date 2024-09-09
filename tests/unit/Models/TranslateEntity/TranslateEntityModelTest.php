<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\TranslateEntity\TranslateEntityModel;

class TranslateEntityModelTest extends TestCase
{
    protected $id = 1;
    protected $key = 'some.key';
    protected $language = 'en';
    protected $phrase = 'some phrase';

    public function testGetId()
    {
        $model = new TranslateEntityModel();
        $model->setData(TranslateEntityModel::ID_FIELD, $this->id);

        $this->assertSame($this->id, $model->getId());
    }

    public function testGetKey()
    {
        $model = new TranslateEntityModel();
        $model->setData(TranslateEntityModel::KEY_FIELD, $this->key);

        $this->assertSame($this->key, $model->getKey());
    }

    public function testGetLanguage()
    {
        $model = new TranslateEntityModel();
        $model->setData(TranslateEntityModel::LANGUAGE_FIELD, $this->language);

        $this->assertSame($this->language, $model->getLanguage());
    }

    public function testGetPhrase()
    {
        $model = new TranslateEntityModel();
        $model->setData(TranslateEntityModel::PHRASE_FIELD, $this->phrase);

        $this->assertSame($this->phrase, $model->getPhrase());
    }

    public function testSetKey()
    {
        $model = new TranslateEntityModel();
        $model->setKey($this->key);

        $this->assertEquals($this->key, $model->getKey());
    }

    public function testSetLanguage()
    {
        $model = new TranslateEntityModel();
        $model->setLanguage($this->language);

        $this->assertEquals($this->language, $model->getLanguage());
    }

    public function testSetPhrase()
    {
        $model = new TranslateEntityModel();
        $model->setPhrase($this->phrase);

        $this->assertEquals($this->phrase, $model->getPhrase());
    }
}
