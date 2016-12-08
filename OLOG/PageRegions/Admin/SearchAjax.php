<?php
namespace OLOG\PageRegions\Admin;

use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\LayoutJSON;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\Permissions;
use OLOG\POSTAccess;
use OLOG\Auth\Operator;

class SearchAjax implements InterfaceAction {

    const SEARCH_FIELD = 'search_field';

    public function url()  {
        return '/admin/search_ajax/';
    }

    public function action() {
        Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));
        $query = POSTAccess::getOptionalPostValue(self::SEARCH_FIELD);
        $ids_arr =  Block::getIdsArrForSearchQuery($query);
        $query = preg_quote($query);
        $content_html = '';
        foreach ($ids_arr as $id) {
            $block = Block::factory($id);
            $action = (new BlockEditAction($id))->url();
            $body = '';
            $p = [];
            if (preg_match_all("#(.{0,20}" . $query . ".{0,20})#ui", $block->getBody(), $p)) {
                foreach ($p[1] as $match) {
                   $match = preg_replace("#" . $query . "#ui", '<b>' . $query . '</b>', htmlentities($match));
                   $body .= "<div>" . $match . "</div>";
                }
            }

            $content_html .= "<div style='padding-bottom: 10px'><div><a href='".  $action ."'>".$block->getInfo()."</a></div> <div>" . $body ."</div></div>";
        }

        $content = ['success' => true, 'html' => $content_html];
        LayoutJSON::render($content, $this);
    }
}