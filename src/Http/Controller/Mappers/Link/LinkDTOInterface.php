<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\Link;

/**
 * Represents a html link entity
 *   a href eq      url
 *   a innerText    name
 *   a title        description
 */
interface LinkDTOInterface
{
    public const NAME_FIELD        = 'name';
    public const DESCRIPTION_FIELD = 'description';
    public const URL_FIELD         = 'url';

    public function getName(): string;

    public function getDescription(): string;

    public function getUrl(): string;
}
