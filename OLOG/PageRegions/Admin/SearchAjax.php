<?php
namespace OLOG\PageRegions\Admin;

use OLOG\InterfaceAction;
use OLOG\Layouts\LayoutJSON;
use OLOG\PageRegions\Block;
use OLOG\POSTAccess;

class SearchAjax implements InterfaceAction {

    const SEARCH_FIELD_INFO = 'search_field_info';
    const SEARCH_FIELD_BODY = 'search_field_body';

    public function url()  {
        return '/admin/search_ajax/';
    }

    public function action() {
        $info = POSTAccess::getOptionalPostValue(self::SEARCH_FIELD_INFO);
        $body = POSTAccess::getOptionalPostValue(self::SEARCH_FIELD_BODY);
        $query = [];
        $params = [];
        if (trim($info)) {
            $query[] = 'info like ?';
            $params[] = '%' . $info . '%';
        }

        if (trim($body)) {
            $query[] = 'body like ?';
            $params[] = '%' . $body . '%';
        }

        $query = implode(' AND ',  $query);

        $ids_arr =  Block::getIdsArrForSearchQuery($query, $params);

        $content_html = '';
        foreach ($ids_arr as $id) {
            $block = Block::factory($id);
            $action = (new BlockEditAction($id))->url();
            $content_html .= "<li><a href='".  $action ."'>".$block->getInfo()."</a> </li>";
        }

        $content = ['success' => true, 'html' => $content_html];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($content);
    }
}