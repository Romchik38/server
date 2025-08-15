<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

/** @deprecated */
interface HttpViewInterface extends ViewInterface
{
    /**
     * Metadata fields
     */
    public const TITLE           = 'title';
    public const DESCRIPTION     = 'description';
    public const HEADER_DATA     = 'header_data';
    public const NAV_DATA        = 'nav_data';
    public const FOOTER_DATA     = 'footer_data';
    public const BREADCRUMB_DATA = 'breadcrumb_data';

    /**
     * Templates
     */
    public const DEFAULT_WRAPPER = '1-column';
}
