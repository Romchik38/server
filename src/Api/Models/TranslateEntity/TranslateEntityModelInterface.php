<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\TranslateEntity;

use Romchik38\Server\Api\Models\ModelInterface;

/**
 * Interface for Translate Entity Model
 *
 * @deprecated
 * */
interface TranslateEntityModelInterface extends ModelInterface
{
    public const ID_FIELD       = 'entity_id';
    public const KEY_FIELD      = 'key';
    public const LANGUAGE_FIELD = 'language';
    public const PHRASE_FIELD   = 'phrase';

    public function getId(): int;

    public function getKey(): string;

    public function getLanguage(): string;

    public function getPhrase(): string;

    public function setKey(string $key): TranslateEntityModelInterface;

    public function setLanguage(string $language): TranslateEntityModelInterface;

    public function setPhrase(string $phrase): TranslateEntityModelInterface;
}
