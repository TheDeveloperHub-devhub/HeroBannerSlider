<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\Repository\HeroBannerRepository;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'DeveloperHub_HeroBannerSlider::edit';

    /** @var HeroBannerRepository */
    private $heroBannerRepository;

    /** @var DataPersistorInterface */
    private $dataPersistor;

    /**
     * @param Context $context
     * @param HeroBannerRepository $heroBannerRepository
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        HeroBannerRepository $heroBannerRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->heroBannerRepository = $heroBannerRepository;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return Page|Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam(HeroBannerInterface::BANNER_ID);
        $heroBanner = null;
        $isExistingBanner = (bool)$bannerId;
        if ($isExistingBanner) {
            $heroBanner = $this->heroBannerRepository->getById($bannerId);
            if (!$heroBanner->getBannerId()) {
                $this->messageManager->addErrorMessage(__('This Hero Banner no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->dataPersistor->set('developerhub_hero_banner', $heroBanner);
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('DeveloperHub_HeroBannerSlider::edit');
        $resultPage->getConfig()->getTitle()->prepend(
            $isExistingBanner ? $heroBanner->getBannerTitle() : __('New Hero Banner')
        );
        return $resultPage;
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $bannerId = $this->getRequest()->getParam(HeroBannerInterface::BANNER_ID);
        $isExistingBanner = (bool)$bannerId;
        if ($isExistingBanner) {
            return $this->_authorization->isAllowed('DeveloperHub_HeroBannerSlider::edit');
        }
        return $this->_authorization->isAllowed('DeveloperHub_HeroBannerSlider::add');
    }
}
