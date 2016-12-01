<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Operator;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Exits;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfacePageToolbarHtml;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\MagnificPopup;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;
use OLOG\Render;

class BlocksListAction extends PageregionsAdminActionsBaseProxy implements
    InterfacePageTitle,
    InterfacePageToolbarHtml,
    InterfaceAction,
    InterfaceTopActionObj
{
	public function url()
	{
		return '/admin/blocks';
	}

	public function pageTitle()
	{
		return 'Блоки';
	}

    function pageToolbarHtml()
    {
        $html = '';

        $create_form_html = \OLOG\CRUD\CRUDForm::html(
            new Block(),
            [
                new CRUDFormRow(
                    'Заголовок',
                    new CRUDFormWidgetInput(Block::_INFO)
                )
            ],
            (new BlockEditAction('{this->id}'))->url()
        );


        $create_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= MagnificPopup::button($create_form_element_id, 'btn btn-primary btn-sm', '<span class="glyphicon glyphicon-plus"></span>');

        $html .= MagnificPopup::popupHtml(
            $create_form_element_id,
            $create_form_html
        );

        return $html;
    }

	public function action()
	{
		$html = '';

		Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

        $html .= SearchForm::html();

		foreach (PageRegionsConfig::getRegionsArr() as $region_name){
            $html .= '<h2><a href="' . (new RegionBlocksListAction($region_name))->url() . '">' . $region_name . '</a></h2>';
            $html .= self::regionBlocksTableHtml($region_name);
        }

        $html .= '<h2>Неназначенные блоки</h2>';
        $html .= self::regionBlocksTableHtml('');

		AdminLayoutSelector::render($html, $this);
	}

	static public function regionBlocksTableHtml($region_name) {
        return  CRUDTable::html(
            Block::class,
            '',
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetTextWithLink(
                        '{this->id}: {this->info}',
                        (new BlockEditAction('{this->id}'))->url()
                    )
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetWeight(
                        ['region' => $region_name]
                    )
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetDelete()
                ),
            ],
            [
                new CRUDTableFilterEqualInvisible('region', $region_name)
            ],
            'weight'
        );
    }

}