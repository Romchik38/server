<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\TranslateEntity;

use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelInterface;
use Romchik38\Server\Models\Model;

class TranslateEntityModel extends Model implements TranslateEntityModelInterface
{
    public function getId(): int
    {
        return (int)$this->getData(TranslateEntityModelInterface::ID_FIELD);
    }

    public function getKey(): string
    {
        return $this->getData(TranslateEntityModelInterface::KEY_FIELD);
    }

    public function getLanguage(): string
    {
        return $this->getData(TranslateEntityModelInterface::LANGUAGE_FIELD);
    }

    public function getPhrase(): string
    {
        return $this->getData(TranslateEntityModelInterface::PHRASE_FIELD);
    }

    public function setKey(string $key): TranslateEntityModelInterface
    {
        $this->setData(TranslateEntityModelInterface::KEY_FIELD, $key);
        return $this;
    }

    public function setLanguage(string $language): TranslateEntityModelInterface
    {
        $this->setData(TranslateEntityModelInterface::LANGUAGE_FIELD, $language);
        return $this;
    }

    public function setPhrase(string $phrase): TranslateEntityModelInterface
    {
        $this->setData(TranslateEntityModelInterface::PHRASE_FIELD, $phrase);
        return $this;
    }
}
