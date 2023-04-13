<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;
use DeveloperHub\HeroBannerSlider\Model\HeroBanner;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\Repository\HeroBannerRepository;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\CollectionFactory;

class MassStatus extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'DeveloperHub_HeroBannerSlider::change_status';

    /** @var TypeListInterface */
    private $typeList;

    /** @var Filter */
    private $filter;

    /** @var CollectionFactory */
    private $collectionFactory;

    /** @var HeroBannerRepository */
    private $heroBannerRepository;

    /**
     * @param Context $context
     * @param TypeListInterface $typeList
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param HeroBannerRepository $heroBannerRepository
     */
    public function __construct(
        Context $context,
        TypeListInterface $typeList,
        Filter $filter,
        CollectionFactory $collectionFactory,
        HeroBannerRepository $heroBannerRepository
    ) {
        parent::__construct($context);
        $this->typeList = $typeList;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->heroBannerRepository = $heroBannerRepository;
    }

    /** @inheritDoc */
    public function execute()
    {
        $updatedBanners = 0;
        $notUpdatedBanners = [];
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $collectionSize = $collection->getSize();

        $status = $this->getRequest()->getParam('status');

        if ($collectionSize == 0) {
            $this->messageManager->addErrorMessage(__('No Banner IDs were provided to change status.'));

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->getResultPage();

            return $resultRedirect->setPath('*/*/');
        }

        /** @var HeroBannerInterface $item */
        foreach ($collection->getItems() as $item) {
            try {
                $heroBanner = $this->heroBannerRepository->getById((int)$item->getBannerId());
                $heroBanner->setData(HeroBannerInterface::IS_ACTIVE, $status);
                $this->heroBannerRepository->save($heroBanner);
                $updatedBanners++;
            } catch (NoSuchEntityException $e) {
                $notUpdatedBanners[] = $item->getBannerId();
            }
        }
        $this->typeList->cleanType("full_page");

        if ($updatedBanners) {
            $statusTitle = $status == HeroBanner::STATUS_ENABLED ? "'Enabled'" : "'Disabled'";
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been updated with status %2.', $updatedBanners, $statusTitle)
            );
        }

        if (count($notUpdatedBanners)) {
            $this->messageManager->addErrorMessage(
                __(
                    'Banner(s) with ID(s) %1 were not found',
                    trim(implode(', ', $notUpdatedBanners))
                )
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->getResultPage();

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Get result page.
     *
     * @return ResultInterface|null
     */
    private function getResultPage(): ?ResultInterface
    {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    }
}
