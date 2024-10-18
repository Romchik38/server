<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Html\Link;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

/**
 * Represents a html link entity 
 *   a href eq      url
 *   a innerText    name
 *   a title        description
 */
interface LinkDTOInterface extends DTOInterface
{
    const NAME_FIELD = 'name';
    const DESCRIPTION_FIELD = 'description';
    const URL_FIELD = 'url';

    public function getName(): string;
    public function getDescription(): string;
    public function getUrl(): string;
}
