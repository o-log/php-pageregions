<?php
namespace OLOG\PageRegions\Admin;

use OLOG\Render;

class SearchForm
{

	public static function html()
	{
		ob_start();
		?>
		<input id="search_ajax" type="text" name="<?= SearchAjax::SEARCH_FIELD ?>" placeholder="Поиск" value="" autocomplete="off">
		<script>
			<?php
			echo Render::callLocaltemplate('js/search.js',
				[
					'search_action' => (new SearchAjax())->url(),
					'search_input_id' => 'search_ajax',
				]);
			?>
		</script>
		<?php
		$html = ob_get_clean();

		return $html;
	}

}