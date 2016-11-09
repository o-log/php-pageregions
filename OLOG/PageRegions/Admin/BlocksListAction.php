<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Operator;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Exits;
use OLOG\HTML;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class BlocksListAction extends PageregionsAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle
{
	public function url()
	{
		return '/admin/blocks';
	}

	public function pageTitle()
	{
		return 'Блоки';
	}

	public function action()
	{
		$html = '';

		Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

		/** @var PageRegionsConfig $config_obj */
		/*
		$config_obj = ConfWrapper::getRequiredValue(PageRegionConstants::MODULE_NAME);

		$regions_arr = $config_obj->getRegionsArr();
		$regions_arr[PageRegionConstants::BLOCK_REGION_NONE] = 'Отключенные блоки';

		foreach ($regions_arr as $region => $region_title) {
			$blocks_ids_arr = BlockHelper::getBlocksIdsArrInRegion($region);

			$html .= '<p><span class="label label-default">' . $region_title . '</p>';
			$html .= '<table class="table table-condensed">';

			foreach ($blocks_ids_arr as $block_id) {
				$block_obj = Block::factory($block_id);

				$html .= '<tr>';
				$html .= '<td width="50"><span class="text-muted" style="margin-right: 10px;">' . $block_obj->getId() . '</span></td>';
				$html .= '<td><a href="/admin2/blocks/edit/' . $block_obj->getId() . '">' . $block_obj->getInfo() . '</a></td>';

				if ($region != PageRegionConstants::BLOCK_REGION_NONE) {
					$html .= '<td align="right"> ';
					$html .= '<a class="glyphicon glyphicon-remove" href="/admin2/blocks/list?a=disable&amp;block_id=' . $block_obj->getId() . '" title="Отключить"></a>';
					$html .= '</td>';
				}

				$html .= '</tr>';
			}
		}

		$html .= '</table>';
		*/

        $html .= '<div class="row">';
        $html .= '<div class="col-lg-6">';

        $html .= '<h2>Регионы</h2>';

		foreach (PageRegionsConfig::getRegionsArr() as $region_name){
            $html .= '<a href="' . (new RegionBlocksListAction($region_name))->url() . '"><div class="well well-sm">' . $region_name . '</a>';
            $block_ids_arr = Block::getIdsArrForRegionByWeightAsc($region_name);
            if ($block_ids_arr) {
                $html .= self::blocksTableHtml($region_name);
            }
            $html .= '</div>';
        }


        $html .= '<div class="well well-sm">';
        $block_ids_arr = Block::getIdsArrForRegionByWeightAsc('');
        if ($block_ids_arr) {
            $html .= self::blocksTableHtml('');
        }
        $html .= '</div>';
        $html .= '</div>';


        $html .= '<div class="col-lg-6">';

        $html .= '<h2>Все блоки</h2>';

		$new_block_obj = new Block();

		$html .= CRUDTable::html(
			Block::class,
			CRUDForm::html(
				$new_block_obj,
				[
					new CRUDFormRow(
						'info',
						new CRUDFormWidgetInput('info')
					)
				]
			),
			[
				new CRUDTableColumn(
					'ID',
					new CRUDTableWidgetText(
						'{this->id}'
					)
				),
				new CRUDTableColumn(
					'Region',
					new CRUDTableWidgetText(
						'{this->region}'
					)
				),
				new CRUDTableColumn(
					'Info',
					new CRUDTableWidgetTextWithLink(
						'{this->info}',
                        (new BlockEditAction('{this->id}'))->url()
					)
				)
			],
            [],
            'region, weight'
		);

        $html .= '</div>';
        $html .= '</div>';

		AdminLayoutSelector::render($html, $this);
	}

	static public function blocksTableHtml($region_name) {
        return  CRUDTable::html(
            Block::class,
            '',
            [
                new CRUDTableColumn(
                    'ID',
                    new CRUDTableWidgetText(
                        '{this->id}'
                    )
                ),
                new CRUDTableColumn(
                    'Info',
                    new CRUDTableWidgetTextWithLink(
                        '{this->info}',
                        (new BlockEditAction('{this->id}'))->url()
                    )
                ),
                new CRUDTableColumn(
                    'Weight',
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