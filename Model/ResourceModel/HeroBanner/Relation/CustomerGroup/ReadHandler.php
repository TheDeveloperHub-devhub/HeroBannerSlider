<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\Relation\CustomerGroup;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner;

class ReadHandler implements ExtensionInterface
{
    /** @var HeroBanner */
    private $resourceBanner;

    /** @param HeroBanner $resourceBanner */
    public function __construct(
        HeroBanner       $resourceBanner
    ) {
        $this->resourceBanner = $resourceBanner;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $connection = $this->resourceBanner->getConnection();
            $customerGroupIds = $connection->fetchCol(
                $connection
                        ->select()
                        ->from($this->resourceBanner->getTable('developerhub_herobanner_customer_group'), ['customer_group_id'])
                        ->where('banner_id = ?', (int)$entity->getId())
            );
            $entity->setData('customer_group_id', $customerGroupIds);
        }
        return $entity;
    }
}
