<?php

declare(strict_types=1);

namespace Romchik38\Server\Views;

use Romchik38\Server\Views\Errors\CantCreateViewException;

class JsonView extends View
{
    public function toString(): string
    {
        // 1. To early
        if ($this->controllerData === null) {
            throw new CantCreateViewException($this::class . ': Controller data was not set');
        }

        // 2. Success
        $data = $this->controllerData->getAllData();
        $result = json_encode($data);
        if ($result !== false) {
            return $result;
        }

        // 3. Error
        throw new CantCreateViewException($this::class . ': error while encoding data to json');
    }
}
