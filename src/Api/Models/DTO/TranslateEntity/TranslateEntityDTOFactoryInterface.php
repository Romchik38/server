<?php

namespace Romchik38\Server\Api\Models\DTO\TranslateEntity;

interface TranslateEntityDTOFactoryInterface
{
    /**
     * Create translate dto entity
     * 
     * @param array $data [key => value, ...]
     */
    public  function create(string $key, array $data): TranslateEntityDTOInterface;
}
