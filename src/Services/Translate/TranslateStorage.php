<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelRepositoryInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;

class TranslateStorage implements TranslateStorageInterface
{
    public function __construct(
        protected readonly TranslateEntityModelRepositoryInterface $translateEntityModelRepository,
        protected readonly TranslateEntityDTOFactoryInterface $translateEntityDTOFactory
    ) {}

    public function getDataByLanguages(array $languages): array
    {
        $models = $this->translateEntityModelRepository->getListByLanguages($languages);
        $dtos = $this->mapModelToDTO($models);
        return $dtos;
    }

    public function getAllDataByKey(string $key): array
    {
        $models = $this->translateEntityModelRepository->getByKey($key);
        $dtos = $this->mapModelToDTO($models);
        return $dtos;
    }

    /**
     * @return TranslateEntityDTOInterface[]
     */
    protected function mapModelToDTO(array $models): array {

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

        $dtos = [];

        foreach ($collection as $itemKey => $languages) {
            $dtos[$itemKey] = $this->translateEntityDTOFactory->create($itemKey, $languages);
        }

        return $dtos;
    }
}