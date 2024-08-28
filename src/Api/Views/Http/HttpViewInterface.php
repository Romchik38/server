<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Views\Http;

use Romchik38\Server\Api\Views\ViewInterface;

interface HttpViewInterface extends ViewInterface
{
    /**
     * Metadata fields
     */
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const HEADER_DATA = 'header_data';
    const NAV_DATA = 'nav_data';
    const FOOTER_DATA = 'footer_data';
    const BREADCRUMB_DATA = 'breadcrumb_data';

    /**
     * Templates
     */
    const DEFAULT_WRAPPER = '1-column';

}
