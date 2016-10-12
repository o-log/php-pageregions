<?php

namespace OLOG\PageRegions;

use OLOG\Router;
use OLOG\Url;

class PageRegions
{
    const INVISIBLE_BLOCKS_DEBUG_COOKIE_NAME = 'invisible_blocks_debug';

    static public function checkBlockComplexVisibility($block_id, $real_path = '')
    {
        $block_obj = Block::factory($block_id);
        $pages = $block_obj->getPages();

        // parse pages

        $pages = str_replace("\r", "\n", $pages);
        $pages = str_replace("\n\n", "\n", $pages);

        $pages_arr = explode("\n", $pages);

        if (count($pages_arr) == 0) {
            return false;
        }

        // check pages

        $visible = false;

        foreach ($pages_arr as $page_filter_str) {
            $page_filter_str = trim($page_filter_str);

            if (strlen($page_filter_str) > 2) {
                // convert filter string to object
                $filter_obj = FilterFactory::getFilter($page_filter_str);

                if ($filter_obj->matchesPage($real_path)) {
                    if ($filter_obj->is_positive) {
                        $visible = TRUE;
                    }

                    if ($filter_obj->is_negative) {
                        $visible = FALSE;
                    }
                }

            }
        }

        return $visible;
    }

    static public function getCurrentPageType(){
        $current_page_type = '';

        $current_action_obj = Router::getCurrentActionObj();
        if ($current_action_obj){
            if ($current_action_obj instanceof InterfacePageRegionsPageType){
                $current_page_type = $current_action_obj->pageRegionsPageType();
            }
        }

        return $current_page_type;
    }

    static public function matchBlockPageTypes($block_id)
    {
        $current_page_type = self::getCurrentPageType();

        $block_obj = Block::factory($block_id);
        $page_types = $block_obj->getPageTypesFilter();

        // parse

        $page_types = str_replace("\r", "\n", $page_types);
        $page_types = str_replace("\n\n", "\n", $page_types);

        $page_types_arr = explode("\n", $page_types);

        if (count($page_types_arr) == 0) {
            return false;
        }

        // check

        $visible = false;

        foreach ($page_types_arr as $page_filter_str) {
            $page_filter_str = trim($page_filter_str);

            if (strlen($page_filter_str) > 2) {
                // convert filter string to object
                $filter_obj = FilterFactory::getFilter($page_filter_str);

                if ($filter_obj->matchesPage($current_page_type)) {
                    if ($filter_obj->is_positive) {
                        $visible = TRUE;
                    }

                    if ($filter_obj->is_negative) {
                        $visible = FALSE;
                    }
                }

            }
        }

        return $visible;
    }

    /**
     * @param string $region
     * @param string $theme
     * @param string $page_url
     * @return string
     */
    static function renderRegion($region, $page_url = '')
    {
        //$_start = microtime(true);

        $output = '';

        $invisible_block_ids_arr = [];

        $blocks_ids_arr = self::getVisibleBlocksIdsArr($region, $page_url, $invisible_block_ids_arr);

        foreach ($blocks_ids_arr as $block_id) {
            $block_content = BlockHelper::getContentByBlockId($block_id);
            if ($block_content != '') {
                $output .= '<!-- ' . $block_id . ' -->';
                $output .= $block_content;
                $output .= '<!-- /' . $block_id . ' -->';
            }
        }

        if (isset($_COOKIE[self::INVISIBLE_BLOCKS_DEBUG_COOKIE_NAME])) {
            foreach ($invisible_block_ids_arr as $block_id => $block_comment) {
                $output .= '<!-- invisible block ' . $block_id . ': ' . $block_comment . ' -->';
            }
        }

        return $output;
    }

    /**
     * @param string $region
     * @param string $theme
     * @param string $page_url
     * @return array
     */
    static function getVisibleBlocksIdsArr($region, $page_url = '', &$invisible_block_ids_arr = [])
    {
        if ($page_url == '') {
            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            $page_url = Url::getCurrentUrlNoGetForm();
        }

        $blocks_ids_arr = BlockHelper::getBlocksIdsArrInRegion($region);

        $visible_blocks_ids_arr = array();

        // Пока передаем в качестве параметра, т.к. медленно отрабатывает currentOperatorHasAnyOfPermissions
        $has_access_to_blocks_for_administrators = BlockHelper::currentUserHasAccessToBlocksForAdministrators();

        foreach ($blocks_ids_arr as $block_id) {
            $comment = 'not visible';
            if (!self::blockIsVisibleOnPage($block_id, $page_url, $has_access_to_blocks_for_administrators, $comment)) {
                $invisible_block_ids_arr[$block_id] = $comment;
                continue;
            }
            $visible_blocks_ids_arr[] = $block_id;
        }

        return $visible_blocks_ids_arr;
    }

    /**
     * @param int $block_id
     * @param string $page_url
     * @param $has_access_to_blocks_for_administrators
     * @return bool
     */
    public static function blockIsVisibleOnPage($block_id, $page_url, $has_access_to_blocks_for_administrators = false, &$out_comment = '')
    {
        $block_obj = Block::factory($block_id);

        if (!$block_obj->getIsPublished()) {
            $out_comment = 'not published';
            return false;
        }

        // Проверяем блок на видимость только для администраторов
        if (!$has_access_to_blocks_for_administrators && $block_obj->isVisibleOnlyForAdministrators()) {
            $out_comment = 'block for admins only';
            return false;
        }

        // Match path if necessary
        if ($block_obj->getPages()) {
            $out_comment = 'url visibility check';
            $visible = self::checkBlockComplexVisibility($block_id, $page_url);
            if (!$visible){
                return false;
            }
        }

        // Match path if necessary
        if ($block_obj->getPageTypesFilter()) {
            $out_comment = 'page types visibility check';
            return self::matchBlockPageTypes($block_id);
        }

        return $visible;
    }

    /**
     * Массив регионов для темы
     * @param $theme_key
     * @return mixed
     */
    /*
    public static function getRegionsArrByTheme($theme_key)
    {
        static $regions_arr = array();

        if (!array_key_exists($theme_key, $regions_arr)) {
            $query = "SELECT info FROM system WHERE type = 'theme' AND name = ?";
            $info = DBWrapper::readField(Constants::DB_NAME_PAGEREGIONS, $query, array($theme_key));
            $info_arr = unserialize($info);
            $regions_arr[$theme_key] = $info_arr['regions'];
        }

        return $regions_arr[$theme_key];
    }
    */
}
