<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Http\Link;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

/**
 * Represents a html link entity
 *   a href eq      url
 *   a innerText    name
 *   a title        description
 */
interface LinkDTOInterface extends DTOInterface
{
    public const NAME_FIELD        = 'name';
    public const DESCRIPTION_FIELD = 'description';
    public const URL_FIELD         = 'url';

    public function getName(): string;

    public function getDescription(): string;

    public function getUrl(): string;
}
