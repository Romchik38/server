<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\TranslateEntity\Sql;

use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelInterface;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelRepositoryInterface;
use Romchik38\Server\Models\Sql\Repository;

use function count;
use function implode;

class TranslateEntityModelRepository extends Repository implements TranslateEntityModelRepositoryInterface
{
    public function getListByLanguages(array $languages): array
    {
        $expression = '';
        $count      = 0;
        $fields     = [];
        foreach ($languages as $language) {
            $count++;
            $fields[] = TranslateEntityModelInterface::LANGUAGE_FIELD . ' = $' . $count;
        }
        if (count($fields) > 0) {
            $expression = 'WHERE ' . implode(' OR ', $fields);
        }

        /** @var TranslateEntityModelInterface[] $list */
        $list = $this->list($expression, $languages);
        return $list;
    }

    public function getByKey(string $key): array
    {
        $expression = 'WHERE ' . $this->table . '.key = $1';
        /** @var TranslateEntityModelInterface[] $list */
        $list = $this->list($expression, [$key]);
        return $list;
    }
}
