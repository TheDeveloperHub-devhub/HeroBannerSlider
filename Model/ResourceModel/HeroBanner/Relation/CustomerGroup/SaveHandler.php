<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\Relation\CustomerGroup;

use Exception;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner;
use Magento\Framework\EntityManager\MetadataPool;

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
     * @throws Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(HeroBannerInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $connection = $entityMetadata->getEntityConnection();
        $oldCustomer = $this->resourceBanner->lookupCustomerGroupIds((int)$entity->getId());
        $newCustomer = (array)$entity->getCustomerGroup();

        if (empty($newCustomer)) {
            $newCustomer = (array)$entity->getCustomerGroupId();
        }

        $table = $this->resourceBanner->getTable('developerhub_herobanner_customer_group');
        $delete = array_diff($oldCustomer, $newCustomer);

        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'customer_group_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCustomer, $oldCustomer);

        if ($insert) {
            $data = [];
            foreach ($insert as $customerGroupId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'customer_group_id' => (int)$customerGroupId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
        return $entity;
    }
}
