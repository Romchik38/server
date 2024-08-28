<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\CompositeId;

use Romchik38\Server\Api\Models\CompositeId\CompositeIdDTOFactoryInterface;
use Romchik38\Server\Api\Models\CompositeId\CompositeIdDTOInterface;
use Romchik38\Server\Models\Errors\DTO\CantCreateDTOException;

class CompositeIdDTOFactory implements CompositeIdDTOFactoryInterface
{
    public function __construct(protected array $idKeys) {}
    
    public function create(array $data): CompositeIdDTOInterface
    {
        $values = [];
        foreach ($this->idKeys as $key) {
            $value = $data[$key] ?? throw new CantCreateDTOException('menu id key does not exist');
            $values[] = $value;
        }

        return new CompositeIdDTO($values);
    }
}
