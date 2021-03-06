<?php

namespace OLOG\PageRegions;

use OLOG\Cache\CacheWrapper;
use OLOG\DB\DBWrapper;
use OLOG\Model\ActiveRecordTrait;
use OLOG\Model\FactoryTrait;
use OLOG\Model\InterfaceDelete;
use OLOG\Model\InterfaceFactory;
use OLOG\Model\InterfaceLoad;
use OLOG\Model\InterfaceSave;
use OLOG\Model\InterfaceWeight;
use OLOG\Model\WeightTrait;

class Block implements
    InterfaceFactory,
    InterfaceLoad,
    InterfaceSave,
    InterfaceDelete,
    InterfaceWeight
{
    use ActiveRecordTrait;
    use FactoryTrait;
    use WeightTrait;

    const DB_ID = PageRegionConstants::DB_ID;
    const DB_TABLE_NAME = 'olog_pageregion_block';
    const _PAGE_TYPES_FILTER = 'page_types_filter';
    const _VISIBLE_ONLY_FOR_ADMINISTRATORS = 'visible_only_for_administrators';
    const _INFO = 'info';

    protected $page_types_filter;
    protected $id;
    protected $created_at_ts = 0; // TODO: initialize in constructor
    protected $is_published = 0;
    protected $weight;
    protected $region = '';
    protected $pages = '+ ^';
    protected $cache = 8; // TODO: constants
    protected $body = '';
    protected $info = '';
    protected $visible_only_for_administrators = 0;
    protected $execute_pseudocode = 0;

    public function beforeSave() {
        $this->initWeight(['region' => $this->getRegion()]);

        $old_region = $this->getOldRegion();

        if ($old_region != $this->getRegion()){
            $this->setWeightRegion();
        }
    }

    public function setWeightRegion(){
        // инициализируем вес в новом регионе
        $max_weight_in_new_region = self::getMaxWeightForContext(['region' => $this->getRegion()]);
        $this->setWeight($max_weight_in_new_region + 1);
    }

    protected function getOldRegion() {
        static $old_region;
        static $has_old_region_in_cache = false;

        if($has_old_region_in_cache){
            return $old_region;
        }

        $old_region = DBWrapper::readField(
            Block::DB_ID,
            'select region from ' . Block::DB_TABLE_NAME . ' where id = ?',
            [$this->getId()]
        );
        $has_old_region_in_cache = true;
        return $old_region;
    }

    public function dropCache(){
        $cache_key = BlockHelper::getBlockContentCacheKey($this->getId());
        CacheWrapper::delete($cache_key);

        $old_region = self::getOldRegion();
        if ($old_region != $this->getRegion()) {
            $region_cache_key = BlockHelper::getBlocksIdsArrInRegionCacheKey($old_region);
            CacheWrapper::delete($region_cache_key);
        }

        $region_cache_key = BlockHelper::getBlocksIdsArrInRegionCacheKey($this->getRegion());
        CacheWrapper::delete($region_cache_key);
    }

    public function afterDelete() {
        self::dropCache();
        self::removeObjFromCacheById($this->getId());
    }

    public function afterSave() {
        $this->removeFromFactoryCache();
        self::dropCache();
    }

    /**
     * Был ли загружен блок
     * @return bool
     */
    public function isLoaded()
    {
        return !empty($this->id);
    }

    /**
     * ID блока
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIsPublished()
    {
        return $this->is_published;
    }

    /**
     * @param int $status
     */
    public function setIsPublished($status)
    {
        $this->is_published = $status;
    }

    /**
     * Вес блока
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Регион блока
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Условия видимости для блока
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param string $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * Контекст кэширования
     * @return int
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param int $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Заголовок блока
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * Содержимое блока
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        $this->execute_pseudocode = Pseudocode::hasPseudocode($body);
    }

    /**
     * @return bool
     */
    public function isVisibleOnlyForAdministrators()
    {
        return (bool)$this->visible_only_for_administrators;
    }

    /**
     * @param bool $is_admin_block
     */
    public function setVisibleOnlyForAdministrators($is_admin_block)
    {
        $this->visible_only_for_administrators = (int)$is_admin_block;
    }

    /**
     * Вывод содержимого блока с учетом PHP - кода
     * @return string
     */
    public function renderBlockContent()
    {
        $content = $this->getBody();

        if ($this->execute_pseudocode) {
            $content = Pseudocode::parse($content);
        }

        return $content;
    }

    public function getPageTypesFilter(){
        return $this->page_types_filter;
    }

    public function setPageTypesFilter($value){
        $this->page_types_filter = $value;
    }

    public function canDelete(&$message)
    {
        #сохраняем регион в статической переменной, чтобы получить его после удаления объекта.
        $this->getOldRegion();
        return true;
    }

    /*
    static public function getIdsArrForRegionByWeightAsc($region)
    {
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where region = ? order by weight',
            [$region]
        );
        return $ids_arr;
    }
    */

    static public function getIdsArrForSearchQuery($query) {
        $ids_arr = DBWrapper::readColumn(
            Block::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where  body like ? OR info like ?',
            ['%' . $query . '%', '%' . $query . '%']
        );

        return $ids_arr;
    }
}