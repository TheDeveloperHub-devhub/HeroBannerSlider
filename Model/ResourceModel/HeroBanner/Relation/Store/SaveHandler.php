<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner;


class SaveHandler implements ExtensionInterface
{
    /** @var MetadataPool */
    private $metadataPool;

    /** @var HeroBanner */
    private $resourceBanner;

    /**
     * @param MetadataPool $metadataPool
     * @param HeroBanner $resourceBanner
     */
    public function __construct(
        MetadataPool $metadataPool,
        HeroBanner       $resourceBanner
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $connection = $entityMetadata->getEntityConnection();
        $oldStores = $this->resourceBanner->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();

        if (empty($newStores)) {
            $newStores = (array)$entity->getStoreId();
        }

        $table = $this->resourceBanner->getTable('developerhub_herobanner_store');
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }
        $insert = array_diff($newStores, $oldStores);

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
        return $entity;
    }
}
