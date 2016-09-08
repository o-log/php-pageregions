<?php

namespace OLOG\PageRegions;

use OLOG\Cache\CacheWrapper;

class BlockHelper
{
    /**
     * @param int $block_id
     * @return string
     */
    public static function getContentByBlockId($block_id)
    {
        $block_obj = Block::factory($block_id);

        $cache_key = BlockHelper::getBlockContentCacheKey($block_id);
        $cache_enabled = true;

        /*
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            $cache_enabled = false;
        }
        */

        if ($block_obj->getCache() == PageRegionConstants::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }

        if ($cache_enabled) {
            $cached_content = CacheWrapper::get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->renderBlockContent();

        if ($cache_enabled) {
            CacheWrapper::set($cache_key, $block_content);
        }

        return $block_content;
    }

    /**
     * @param int $block_id
     * @return string|null
     */
    public static function getBlockContentCacheKey($block_id)
    {
        $block_obj = Block::factory($block_id);

        $cid_parts = array('block_content');
        $cid_parts[] = $block_obj->getId();

        // Кешируем блоки по полному урлу $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
        // Т.к. содержимое блока может различаться. Например, страница телепрограммы по дням.
        if ($block_obj->getCache() == PageRegionConstants::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return implode(':', $cid_parts);
    }

    /**
     * @return bool
     */
    public static function currentUserHasAccessToBlocksForAdministrators()
    {
        // TODO: rewrite
        /*
        return OperatorHelper::currentOperatorHasAnyOfPermissions(array(
            OperatorPermission::PERMISSION_EDIT_BLOCK,
            OperatorPermission::PERMISSION_EDIT_ARTICLE_NODE_CONTENT,
            OperatorPermission::PERMISSION_EDIT_PAGE_NODE_CONTENT
        ));
        */
    }

    /**
     * @param string $theme
     * @return array
     */
    /*
    public static function getBlocksIdsArrByTheme($theme)
    {
        $blocks_ids_arr = DBWrapper::readColumn(Constants::DB_NAME_NEWS,
            "SELECT id FROM blocks
            WHERE theme = ?
            ORDER BY region, weight, info",
            array(
                $theme
            )
        );

        return $blocks_ids_arr;
    }
    */

    /**
     * @param string $region
     * @param string $theme
     * @return array
     */
    public static function getBlocksIdsArrInRegion($region)
    {
        $cache_key = self::getBlocksIdsArrInRegionCacheKey($region);

        $blocks_ids_arr = \OLOG\Cache\CacheWrapper::get($cache_key);
        if ($blocks_ids_arr !== false) {
            return $blocks_ids_arr;
        }
        
        $blocks_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            Block::DB_ID,
            "SELECT id FROM " . Block::DB_TABLE_NAME . " WHERE region = ? ORDER BY weight",
            array($region)
        );
        
        \OLOG\Cache\CacheWrapper::set($cache_key, $blocks_ids_arr, 3600);

        return $blocks_ids_arr;
    }

    /*
    public static function clearBlocksIdsArrInRegionCache($region, $theme)
    {
        $cache_key = BlockHelper::getBlocksIdsArrInRegionCacheKey($region, $theme);
        CacheWrapper::delete($cache_key);
    }
    */

    public static function getBlocksIdsArrInRegionCacheKey($region_id)
    {
        return 'blocks_in_region_' . $region_id;
    }
}
