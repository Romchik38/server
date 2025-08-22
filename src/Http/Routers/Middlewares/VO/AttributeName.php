<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares\VO;

use Romchik38\Server\Domain\VO\Text\NonEmpty;

final class AttributeName extends NonEmpty
{
    public const NAME = 'router middleware attribute name';
}
