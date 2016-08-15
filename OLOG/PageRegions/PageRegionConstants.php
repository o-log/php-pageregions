<?php

namespace OLOG\PageRegions;

class PageRegionConstants
{
    const DB_ID = 'db_pageregions';

    const BLOCK_REGION_NONE = '';

    const BLOCK_NO_CACHE = 0;
    const BLOCK_CACHE_PER_PAGE = 4;
    const BLOCK_CACHE_GLOBALLY = 8;

    const CACHE_ARR = [
        self::BLOCK_NO_CACHE => 'No cache',
        self::BLOCK_CACHE_PER_PAGE => 'Per page',
        self::BLOCK_CACHE_GLOBALLY => 'Globally'
    ];
}