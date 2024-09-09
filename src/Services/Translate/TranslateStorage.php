<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOFactoryInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelRepositoryInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;

class TranslateStorage implements TranslateStorageInterface
{

    protected array $hash = [];

    public function __construct(
        protected readonly TranslateEntityModelRepositoryInterface $translateEntityModelRepository,
        protected readonly TranslateEntityDTOFactoryInterface $translateEntityDTOFactory
    ) {}

    public function getDataByLanguages(array $languages): array
    {
        // 1. return data if it is already was created
        if (count($this->hash) > 0) {
            return $this->hash;
        }

        // 2. creating data
        $models = $this->translateEntityModelRepository->getListByLanguages($languages);

        $collection = [];

        foreach ($models as $model) {
            $key = $model->getKey();
            if (array_key_exists($key, $collection) === true) {
                $languages = $collection[$key];
                $languages[$model->getLanguage()] = $model->getPhrase();
                $collection[$key] = $languages;
            } else {
                $collection[$key] = [$model->getLanguage() => $model->getPhrase()];
            }
        }

        foreach ($collection as $itemKey => $languages) {
            $this->hash[$itemKey] = $this->translateEntityDTOFactory->create($itemKey, $languages);
        }

        return $this->hash;
    }
}
