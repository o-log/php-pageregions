<?php

namespace OLOG\PageRegions\Admin;

use OLOG\Auth\Admin\CurrentUserNameTrait;
use OLOG\Auth\Operator;
use OLOG\BT\BT;
use OLOG\BT\InterfaceBreadcrumbs;
use OLOG\BT\InterfacePageTitle;
use OLOG\BT\InterfaceUserName;
use OLOG\BT\Layout;
use OLOG\ConfWrapper;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Exits;
use OLOG\PageRegions\Block;
use OLOG\PageRegions\BlockHelper;
use OLOG\PageRegions\PageRegionConstants;
use OLOG\PageRegions\PageRegionsConfig;
use OLOG\PageRegions\Permissions;

class RegionBlocksListAction implements InterfaceBreadcrumbs, InterfacePageTitle, InterfaceUserName
{
    use CurrentUserNameTrait;

    protected $region_name;

    static public function getUrl($region_name = '(\w+)')
    {
        return '/admin/regionblocks/' . $region_name;
    }

    public function currentPageTitle()
    {
        return self::pageTitleForRegion($this->region_name);
    }

    static public function pageTitleForRegion($region_name)
    {
        return 'Регион ' . $region_name;
    }

    public function currentBreadcrumbsArr()
    {
        return self::breadcrumbsArrForRegion($this->region_name);
    }

    static public function breadcrumbsArrForRegion($region_name)
    {
        return array_merge(BlocksListAction::breadcrumbsArr(),
            [
            BT::a(self::getUrl($region_name), self::pageTitleForRegion($region_name))
        ]);
    }

    public function action($region_name)
    {
        Exits::exit403If(!Operator::currentOperatorHasAnyOfPermissions([Permissions::PERMISSION_PAGEREGIONS_MANAGE_BLOCKS]));

        $this->region_name = $region_name;
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
                        BlockEditAction::getUrl('{this->id}')
                    )
                )
            ],
            [
                new CRUDTableFilterEqualInvisible('region', $region_name)
            ],
            'weight'
        );

        Layout::render($html, $this);

    }
}