<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Operator;
use OLOG\BT\Layout;
use OLOG\ConfWrapper;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\Exits;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\BlockHelper;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class BlocksListAction
{
    static public function getUrl(){
        return '/admin/blocks';
    }

    public function action(){
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
                    'Region',
                    new CRUDTableWidgetText(
                        '{this->region}'
                    )
                ),
                new CRUDTableColumn(
                    'Weight',
                    new CRUDTableWidgetText(
                        '{this->weight}'
                    )
                ),
                new CRUDTableColumn(
                    'Info',
                    new CRUDTableWidgetTextWithLink(
                        '{this->info}',
                        BlockEditAction::getUrl('{this->id}')
                    )
                )
            ]
        );

        Layout::render($html, $this);
        
    }
}