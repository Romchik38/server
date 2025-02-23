<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelRepositoryInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;

use function array_key_exists;

class TranslateStorage implements TranslateStorageInterface
{
    public function __construct(
        protected readonly TranslateEntityModelRepositoryInterface $translateEntityModelRepository,
        protected readonly TranslateEntityDTOFactoryInterface $translateEntityDtoFactory
    ) {
    }

    public function getDataByLanguages(array $languages): array
    {
        $models = $this->translateEntityModelRepository->getListByLanguages($languages);
        return $this->mapModelToDTO($models);
    }

    public function getAllDataByKey(string $key): array
    {
        $models = $this->translateEntityModelRepository->getByKey($key);
        return $this->mapModelToDTO($models);
    }

    /**
     * Returns a hash [key => dto, ...]
     *
     * @param array<int, TranslateEntityModelInterface> $models
     * @return array<string,TranslateEntityDTOInterface>
     */
    protected function mapModelToDTO(array $models): array
    {
        $collection = [];

        foreach ($models as $model) {
            $key = $model->getKey();
            if (array_key_exists($key, $collection) === true) {
                $languages                        = $collection[$key];
                $languages[$model->getLanguage()] = $model->getPhrase();
                $collection[$key]                 = $languages;
            } else {
                $collection[$key] = [$model->getLanguage() => $model->getPhrase()];
            }
        }

        $hash = [];

        foreach ($collection as $itemKey => $languages) {
            $hash[$itemKey] = $this->translateEntityDtoFactory->create($itemKey, $languages);
        }

        return $hash;
    }
}
