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

    public function beforeSave(){
        $this->initWeight(['region' => $this->getRegion()]);
        $this->dropCache();
    }

    public function setWeightRegion(){
        // инициализируем вес в новом регионе
        $max_weight_in_new_region = self::getMaxWeightForContext(['region' => $this->getRegion()]);
        $this->setWeight($max_weight_in_new_region + 1);
    }

    public function dropCache(){

        if (!is_null($this->getId())){

            CacheWrapper::delete( BlockHelper::getBlockContentCacheKey( $this->getId()) );
            $old_region = DBWrapper::readField(
                Block::DB_ID,
                'select region from ' . Block::DB_TABLE_NAME . ' where id = ?',
                [$this->getId()]
            );
            if ($old_region != $this->getRegion()){
                $this->processRegionChange();
                $key = BlockHelper::getBlocksIdsArrInRegionCacheKey($old_region);
                CacheWrapper::delete($key);
            }

        }
        $key = BlockHelper::getBlocksIdsArrInRegionCacheKey($this->getRegion());
        CacheWrapper::delete($key);

    }

    /*
    public static function afterUpdate($id)
    {
        self::removeObjFromCacheById($id);

        $block_obj = Block::factory($id);
        Logger::logObjectEvent($block_obj, 'изменение');
    }
      */

    public function afterDelete()
    {
        self::dropCache();
        self::removeObjFromCacheById($this->getId());
        BlockHelper::clearBlocksIdsArrInRegionCache($this->getRegion());


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
}