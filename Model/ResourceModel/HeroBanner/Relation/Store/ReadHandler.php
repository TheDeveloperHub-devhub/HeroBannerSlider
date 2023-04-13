<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner;

class ReadHandler implements ExtensionInterface
{
    /** @var HeroBanner */
    private $resourceBanner;

    /** @param HeroBanner $resourceBanner */
    public function __construct(
        HeroBanner $resourceBanner
    ) {
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
        if ($entity->getId()) {
            $stores = $this->resourceBanner->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
