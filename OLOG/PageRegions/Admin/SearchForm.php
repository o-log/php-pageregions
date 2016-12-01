<?php
namespace OLOG\PageRegions\Admin;

use OLOG\Render;

class SearchForm {

    public static function html() {
        ob_start();
        ?>
        <script>
           <?= Render::callLocaltemplate('js/search.js',
               [
                   'search_field_info' => SearchAjax::SEARCH_FIELD_INFO,
                   'search_field_body' => SearchAjax::SEARCH_FIELD_BODY,
               ]);?>
        </script>

        <div id="search_form">
        <input type="text" name="<?=SearchAjax::SEARCH_FIELD_INFO ?>" placeholder="Название" value=""  autocomplete="off">
        <input type="text" name="<?=SearchAjax::SEARCH_FIELD_BODY ?>" placeholder="Body" value="" autocomplete="off">
        </div>
        <ul class="search_result"></ul>


        <?php
        $html = ob_get_clean();

        return $html;
    }
}