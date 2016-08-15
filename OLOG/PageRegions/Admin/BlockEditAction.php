<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Admin\CurrentUserNameTrait;
use OLOG\Auth\Auth;
use OLOG\Auth\Operator;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
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
use OLOG\PageRegions\Block;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class BlockEditAction implements InterfaceBreadcrumbs, InterfacePageTitle, InterfaceUserName
{
	use CurrentUserNameTrait;
	protected $block_id;

	static public function getUrl($block_id = '(\d+)')
	{
		return '/admin/block/' . $block_id;
	}

	public function currentPageTitle()
	{
		return self::pageTitle($this->block_id);
	}

	static public function pageTitle($block_id)
	{
		return 'Блоки ' . $block_id;
	}

	public function currentBreadcrumbsArr()
	{
		return self::breadcrumbsArr($this->block_id);
	}

	static public function breadcrumbsArr($block_id)
	{
	    $base_arr = BlocksListAction::breadcrumbsArr();

        $block_obj = Block::factory($block_id, false);
        if (is_null($block_obj)){
            return [];
        }

        if ($block_obj->getRegion() != ''){
            $base_arr = RegionBlocksListAction::breadcrumbsArrForRegion($block_obj->getRegion());
        }

		return array_merge($base_arr, [BT::a(self::getUrl($block_id), self::pageTitle($block_id))]);
	}

	public function action($block_id)
	{
		Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

		$this->block_id = $block_id;

        $html = '';
        $block_obj = Block::factory($block_id, false); // block may be deleted
        if (is_null($block_obj)){
            $html .= '<div class="alert">Блок не найден. ' . BT::a(BlocksListAction::getUrl(), 'Перейти к списку блоков') . '.</div>';
            Layout::render($html, $this);
            return;
        }

        CRUDTable::executeOperations();

        $delete_widget_obj = new CRUDTableWidgetDelete();
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
					new CRUDFormWidgetAceTextarea('body')
				),
                new CRUDFormRow(
                    'Execute pseudocode',
                    new CRUDFormWidgetRadios('execute_pseudocode', [0 => 'No', 1 => 'Yes'])
                ),
				new CRUDFormRow(
					'Pages',
					new CRUDFormWidgetTextarea('pages')
				),
				new CRUDFormRow(
					'Cache',
					new CRUDFormWidgetInput('cache')
				),
			]
		);

        $html .= '<h2>Rendered block</h2>';

        $html .= '<div style="border: 5px solid red;">' . $block_obj->renderBlockContent() . '</div>';

		Layout::render($html, $this);
	}
}