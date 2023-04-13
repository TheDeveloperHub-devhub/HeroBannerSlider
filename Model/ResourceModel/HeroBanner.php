<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;

class HeroBanner extends AbstractDb
{
    const MAIN_TABLE = "developerhub_herobanner";

    private $store = null;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var EntityManager */
    private $entityManager;

    /** @var MetadataPool */
    private $metadataPool;

    /** @var DateTime */
    private $dateTime;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param DateTime $dateTime
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $connectionName
        );
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->dateTime = $dateTime;
    }

    /** @inheritDoc */
    protected function _construct()
    {
        $this->_init(
            self::MAIN_TABLE,
            HeroBannerInterface::BANNER_ID
        );
    }

    /** @inheritDoc */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(HeroBannerInterface::class)->getEntityConnection();
    }

    /**
     * @param AbstractModel $object
     * @return HeroBanner
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreatedAt($this->dateTime->gmtDate());
        }
        $object->setUpdatedAt($this->dateTime->gmtDate());

        return parent::_beforeSave($object);
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * @param $identifier
     * @param $store
     * @param $isActive
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->join(
                ['cps' => $this->getTable('developerhub_herobanner_store')],
                'cp.' . $linkField . ' = cps.' . $linkField,
                []
            )
            ->where('cp.banner_id = ?', $identifier)
            ->where('cps.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * @param $bannerId
     * @return array
     * @throws LocalizedException
     */
    public function lookupStoreIds($bannerId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('developerhub_herobanner_store')], 'store_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :banner_id');

        return $connection->fetchCol($select, ['banner_id' => (int)$bannerId]);
    }

    /**
     * @param $bannerId
     * @return array
     * @throws LocalizedException
     */
    public function lookupCustomerGroupIds($bannerId)
    {
        $connection = $this->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('developerhub_herobanner_customer_group')], 'customer_group_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :banner_id');

        return $connection->fetchCol($select, ['banner_id' => (int)$bannerId]);
    }

    /**
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore($this->store);
    }

    /** @inheritDoc */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /** @inheritDoc */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
