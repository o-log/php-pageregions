<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Operator;
use OLOG\Layouts\InterfaceTopActionObj;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Exits;
use OLOG\InterfaceAction;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\InterfacePageTitle;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\Permissions;

class RegionBlocksListAction extends PageregionsAdminActionsBaseProxy implements
    InterfaceAction,
    InterfacePageTitle,
    InterfaceTopActionObj
{
    protected $region_name;

    public function topActionObj()
    {
        return new BlocksListAction();
    }

    public function __construct($region_name)
    {
        $this->region_name = $region_name;
    }

    public function url()
    {
        return '/admin/regionblocks/' . $this->region_name;
    }

    static public function urlMask()
    {
        return '/admin/regionblocks/(\w+)';
    }

    public function pageTitle()
    {
        return 'Регион ' . $this->region_name;
    }

    public function action()
    {
        Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

        $region_name = $this->region_name;
        $html = '';

        $new_block_obj = new Block();
        $new_block_obj->setRegion($region_name);

        $html .= CRUDTable::html(
            Block::class,
            CRUDForm::html(
                $new_block_obj,
                [
                    new CRUDFormRow(
                        'info',
                        new CRUDFormWidgetInput('info')
                    ),
                    new CRUDFormRow(
                        'Region',
                        new CRUDFormWidgetInput('region')
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
                    'Weight',
                    new CRUDTableWidgetWeight(
                        ['region' => $region_name]
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
            [
                new CRUDTableFilterEqualInvisible('region', $region_name)
            ],
            'weight'
        );

	    AdminLayoutSelector::render($html, $this);
    }
}