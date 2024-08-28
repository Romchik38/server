<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\CompositeId;

use Romchik38\Server\Api\Models\CompositeId\CompositeIdDTOInterface;
use Romchik38\Server\Models\DTO;

class CompositeIdDTO extends DTO implements CompositeIdDTOInterface
{

    public function __construct(array $data)
    {
        foreach ($data as $key => $val) {
            $this->data[$key] = $val;
        }
    }

    public function getIdKeys(): array
    {
        return array_keys($this->data);
    }
}
