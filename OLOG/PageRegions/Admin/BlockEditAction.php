<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Operator;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetAceTextarea;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\Exits;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class BlockEditAction extends PageregionsAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle,
    InterfaceTopActionObj
{
	protected $block_id;

    public function topActionObj()
    {
        $block_obj = Block::factory($this->block_id);

        if ($block_obj->getRegion()){
            return new RegionBlocksListAction($block_obj->getRegion());
        }

        return new BlocksListAction();
    }

    public function __construct($block_id)
    {
        $this->block_id = $block_id;
    }

    public function url()
    {
        return '/admin/block/' . $this->block_id;
    }

    static public function urlMask()
    {
        return '/admin/block/(\d+)';
    }

    public function pageTitle()
	{
		return 'Блоки ' . $this->block_id;
	}

	public function action()
	{
		Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

        $block_id = $this->block_id;

        $html = '';
        $block_obj = Block::factory($block_id, false); // block may be deleted
        if (is_null($block_obj)){
            $html .= '<div class="alert">Блок не найден. ' . HTML::a((new BlocksListAction())->url(), 'Перейти к списку блоков') . '.</div>';
	        AdminLayoutSelector::render($html, $this);
            return;
        }

        CRUDTable::executeOperations();

        $delete_widget_obj = new CRUDTableWidgetDelete('Удалить', 'btn btn-default');
        $html .= '<div>' . $delete_widget_obj->html($block_obj) . '</div>';

		$html .= CRUDForm::html(
			$block_obj,
			[
				new CRUDFormRow(
					'Info',
					new CRUDFormWidgetInput('info')
				),
				new CRUDFormRow(
					'Region',
					new CRUDFormWidgetOptions(
						'region',
						array_merge(
						    ['' => ''],
						    PageRegionsConfig::getRegionsArr()
                        )
					)
				),
				new CRUDFormRow(
					'Weight',
					new CRUDFormWidgetInput('weight')
				),
				new CRUDFormRow(
					'Published',
					new CRUDFormWidgetRadios('is_published', [0 => 'No', 1 => 'Yes'])
				),
				new CRUDFormRow(
					'Body',
					new CRUDFormWidgetAceTextarea('body'),
                    '<p>Пример вызова php-метода:</p>
<pre>
##@call_method
\Components\RightColumnTeasersList\RightColumnTeasersListComponent::getHtml
##@call_method_end
</pre>
<p>Если метод принимает параметры - их можно указать после имени метода, значение каждого параметра - на отдельной строке.</p>
'
				),
                new CRUDFormRow(
                    'Execute pseudocode',
                    new CRUDFormWidgetRadios('execute_pseudocode', [0 => 'No', 1 => 'Yes'])
                ),
                new CRUDFormRow(
                    'Pages',
                    new CRUDFormWidgetTextarea('pages'),
                    '<p>Одна строка - один фильтр. Каждый фильтр должен начинаться с символов + или - и потом пробела.</p>
                    <p>После символа + или - и пробела указывается маска адреса. + включает показ блока на этих адресах, а - выключает.</p>
                    <p>Вот пример фильтра для болка, который показывается на всех страницах CS:GO, кроме Dreamhack.</p>
<pre>
+ csgo
- csgo/dreamhack
</pre>
                    <p>Маска - это регулярное выражение.</p>

                    <p>Т.е. "csgo/dreamhack" - это значит csgo/dreamhack может входить в адрес в любом месте.</p>
                    <p>"^/csgo/dreamhack" - это значит должно входить именно в начале адреса.</p>
                    <p>Адреса начинаются со "/".</p>
                    <p>Главная страница - это "^/$".</p>'
                ),
                new CRUDFormRow(
                    'Page types',
                    new CRUDFormWidgetTextarea(Block::_PAGE_TYPES_FILTER),
	                '<p>Одна строка - один фильтр. Каждый фильтр должен начинаться с символов + или - и потом пробела.</p>
					<p>После символа + или - и пробела указывается маска адреса.</p>
                    <p>Вот пример фильтра для болка, который показывается на всех типах страниц Article, кроме Main.</p>
<pre>
+ ^Article$
- ^Main$
</pre>'
                ),
				new CRUDFormRow(
					'Cache',
					new CRUDFormWidgetOptions('cache', PageRegionConstants::CACHE_ARR)
				),
                new CRUDFormRow(
                    'Visible only for administrators',
                    new CRUDFormWidgetOptions(Block::_VISIBLE_ONLY_FOR_ADMINISTRATORS, [0 => 'Нет', 1 => 'Да'])
                ),
			]
		);

        $html .= '<h2>Rendered block</h2>';

        $html .= '<div style="border: 5px solid red;">';
        $html .= $block_obj->renderBlockContent();
        $html .= '</div>';

		AdminLayoutSelector::render($html, $this);
	}
}