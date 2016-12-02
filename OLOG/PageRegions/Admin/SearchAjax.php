<?php
namespace OLOG\PageRegions\Admin;

use OLOG\InterfaceAction;
use OLOG\Layouts\LayoutJSON;
use OLOG\PageRegions\Block;
use OLOG\POSTAccess;

class SearchAjax implements InterfaceAction {

    const SEARCH_FIELD = 'search_field';

    public function url()  {
        return '/admin/search_ajax/';
    }

    public function action() {
        $query = POSTAccess::getOptionalPostValue(self::SEARCH_FIELD);
        $ids_arr =  Block::getIdsArrForSearchQuery($query);
        $query = preg_quote($query);
        $content_html = '';
        foreach ($ids_arr as $id) {
            $block = Block::factory($id);
            $action = (new BlockEditAction($id))->url();
            $body = '';
            $p = [];
            if (preg_match_all("#(.{0,50}" . $query . ".{0,50})#uim", $block->getBody(), $p)) {
                $body .= "<pre>" . $p[1][0] . "</pre>";
                foreach ($p[1] as $match) {
                   $body .= "<pre>" . $match . "</pre>";
                }
            }
            $content_html .= "<li><a href='".  $action ."'>".$block->getInfo()."</a> " . "#(.{0,50}" . $query . ".{0,50})#im" . " <div style='color:#CCC; font-size:10px'>" . $body ."</div> </li>";
        }

        $content = ['success' => true, 'html' => $content_html];
        LayoutJSON::render($content, $this);
    }
}