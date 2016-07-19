<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Auth;
use OLOG\Auth\Operator;
use OLOG\BT\Layout;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetAceTextarea;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTable;
use OLOG\Exits;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class BlockEditAction
{
    static public function getUrl($block_id = '(\d+)')
    {
        return '/admin/block/' . $block_id;
    }

    public function action($block_id)
    {
        Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

        $block_obj = Block::factory($block_id);

        $html = CRUDForm::html(
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
                        PageRegionsConfig::getRegionsArr()
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
                    'Pages',
                    new CRUDFormWidgetTextarea('pages')
                ),
                new CRUDFormRow(
                    'Cache',
                    new CRUDFormWidgetInput('cache')
                ),
            ]
        );

        Layout::render($html, $this);
    }
}