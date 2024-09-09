<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\TranslateEntity\Sql;

use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelRepositoryInterface;
use Romchik38\Server\Models\Sql\Repository;
use Romchik38\Server\Api\Models\TranslateEntity\TranslateEntityModelInterface;

class TranslateEntityModelRepository extends Repository implements TranslateEntityModelRepositoryInterface
{

    public function getListByLanguages(array $languages): array
    {
        $expression =  '';
        $count = 0;
        $fields = [];
        foreach ($languages as $language) {
            $count++;
            $fields[] = TranslateEntityModelInterface::LANGUAGE_FIELD . ' = $' . $count;
        }
        if (count($fields) > 0) {
            $expression = ' WHERE ' . implode(' OR ', $fields);
        }

        $list = $this->list($expression, $languages);
        return $list;
    }
}
