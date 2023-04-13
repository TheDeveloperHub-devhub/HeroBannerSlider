<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\HeroBanner\DataProvider;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\Collection as HeroBannerCollection;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\CollectionFactory;

class HeroBanner extends Template implements IdentityInterface
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var TimezoneInterface */
    private $timezoneInterface;

    /** @var CollectionFactory */
    private $heroBannerCollectionFactory;

    /** @var Collection */
    private $dataCollection;

    /** @var Session */
    private $customerSesion;

    /** @var FilterProvider */
    private $filterProvider;

    /**
     * @param Context $context
     * @param TimezoneInterface $timezoneInterface
     * @param CollectionFactory $heroBannerCollectionFactory
     * @param Collection $dataCollection
     * @param Session $customerSesion
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezoneInterface,
        CollectionFactory $heroBannerCollectionFactory,
        Collection $dataCollection,
        Session $customerSesion,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $context->getStoreManager();
        $this->timezoneInterface = $timezoneInterface;
        $this->heroBannerCollectionFactory = $heroBannerCollectionFactory;
        $this->dataCollection = $dataCollection;
        $this->customerSesion = $customerSesion;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return string
     */
    public function getConfig($config)
    {
        return $this->_scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array|mixed|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getBanner()
    {
        if (!$this->hasData('banner')) {
            $bannerCollection = $this->heroBannerCollectionFactory->create()
                ->addFilter('is_active', 1)
                ->addFieldToFilter('store', $this->storeManager->getStore()->getId())
                ->addFieldToFilter('customer', $this->customerSesion->getCustomerGroupId());
            $bannerCollection->getSelect()->group('banner_id');
            $bannerCollection->getSelect()
                ->order(new \Zend_Db_Expr(HeroBannerInterface::POSITION . ' ' . HeroBannerCollection::SORT_ORDER_ASC));
            $collection = $this->dataCollection;

            $currentDate = strtotime($this->timezoneInterface->formatDate());

            foreach ($bannerCollection as $banner) {
                $data = $banner->getData();

                $bannerStartTime = '';
                $bannerEndTime = '';
                if ($banner->getData('start_time') !== null) {
                    $bannerStartTime = strtotime($banner->getData('start_time'));
                }
                if ($banner->getData('end_time') !== null) {
                    $bannerEndTime = strtotime($banner->getData('end_time'));
                }
                if (($currentDate >= $bannerStartTime && $currentDate <= $bannerEndTime) || ($currentDate >= $bannerStartTime && $bannerEndTime == '')) {
                    $rowObj = new DataObject();
                    $rowObj->setData($data);
                    $collection->addItem($rowObj);
                }
            }
            $this->setData('banner', $collection);
        }

        return $this->getData('banner');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [\DeveloperHub\HeroBannerSlider\Model\HeroBanner::CACHE_TAG . '_' . 'list'];
    }

    /** @return string */
    public function getMediaPath($path = null)
    {
        try {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . DataProvider::FILE_PATH . $path;
        } catch (NoSuchEntityException $exception) {
            return $path;
        }
    }

    /**
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function getCmsFilterContent($value = '')
    {
        return $this->filterProvider->getPageFilter()->filter($value);
    }
}
