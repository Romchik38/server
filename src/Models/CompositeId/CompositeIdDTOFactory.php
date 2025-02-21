<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\CompositeId;

use Romchik38\Server\Api\Models\CompositeId\CompositeIdDTOFactoryInterface;
use Romchik38\Server\Api\Models\CompositeId\CompositeIdDTOInterface;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

class CompositeIdDTOFactory implements CompositeIdDTOFactoryInterface
{
    /** @param array<int,string> */
    public function __construct(protected array $idKeys) {}
    
    public function create(array $data): CompositeIdDTOInterface
    {
        $values = [];
        foreach ($this->idKeys as $key) {
            $value = $data[$key] ?? throw new InvalidArgumentException('Composite id key is required:' . $key);
            $values[] = $value;
        }

        return new CompositeIdDTO($values);
    }
}
