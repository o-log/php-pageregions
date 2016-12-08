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
                    'search_field' => SearchAjax::SEARCH_FIELD,
                ]);?>
        </script>

        <div id="search_form">
            <input type="text" name="<?=SearchAjax::SEARCH_FIELD ?>" placeholder="Поиск" value="" autocomplete="off">
        </div>
        <div class="search_result"></div>


        <?php
        $html = ob_get_clean();

        return $html;
    }
}