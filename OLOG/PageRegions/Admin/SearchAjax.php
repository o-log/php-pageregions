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
            if (preg_match("#(.{0,100}" . $query . ".{0,100})#im",$block->getBody(), $p)) {
                $body = $p[1];
            }

            $content_html .= "<li><a href='".  $action ."'>".$block->getInfo()."</a> " . " <div style='color:#CCC; font-size:10px'>" . $body ."</div> </li>";
        }

        $content = ['success' => true, 'html' => $content_html];
        LayoutJSON::render($content, $this);
    }
}