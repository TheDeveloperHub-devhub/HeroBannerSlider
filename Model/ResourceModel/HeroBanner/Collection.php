<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\HeroBanner;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\AbstractCollection;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner as HeroBannerResource;

class Collection extends AbstractCollection
{
    /**
     * Primary field name of table
     *
     * @var string
     */
    protected $_idFieldName = 'banner_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /** @inheritDoc */
    protected function _construct()
    {
        $this->_init(
            HeroBanner::class,
            HeroBannerResource::class
        );
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * @param $field
     * @param $condition
     * @return $this|Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case "store":
                $condition = $this->getConnection()->prepareSqlCondition('store.store_id', $condition);
                $this->getSelect()->join(
                    ['store' => $this->getTable('developerhub_herobanner_store')],
                    'main_table.banner_id = store.banner_id',
                    []
                );
                $this->getSelect()->where(sprintf('(%s OR store.store_id = 0)', $condition));
                break;
            case "customer":
                $condition = $this->getConnection()->prepareSqlCondition('customer.customer_group_id', $condition);
                $this->getSelect()->join(
                    ['customer' => $this->getTable('developerhub_herobanner_customer_group')],
                    'main_table.banner_id = customer.banner_id',
                    []
                );
                $this->getSelect()->where(sprintf('(%s)', $condition));
                break;
            default:
                parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }

    /**
     * @param int $bannerId
     * @return array
     */
    public function addStoreFilter($bannerId)
    {
        $this->join(
            ['banner_store' => $this->getTable('developerhub_herobanner_store')],
            'main_table.banner_id= banner_store.banner_id',
            'store_id'
        );
        $this->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);
        $this->addFieldToFilter('banner_id', ['eq' => $bannerId]);
        $this->addFieldToFilter('status', HeroBanner::STATUS_ENABLED);
        foreach ($this as $data) {
            $storeIds[] = $data->getData('store_id');
        }
        return $storeIds;
    }
    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $this->performAfterLoad($this->getTable('developerhub_herobanner_store'), $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $this->joinStoreRelationTable($this->getTable('developerhub_herobanner_store'), $entityMetadata->getLinkField());
    }
}
