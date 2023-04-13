<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use DeveloperHub\HeroBannerSlider\Model\Uploader;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\HeroBanner;
use DeveloperHub\HeroBannerSlider\Model\HeroBannerFactory;
use DeveloperHub\HeroBannerSlider\Model\Repository\HeroBannerRepository;

class Save extends Action
{
    /** @var HeroBannerFactory */
    private $heroBannerFactory;

    /** @var HeroBannerRepository */
    private $heroBannerRepository;

    /** @var TypeListInterface */
    private $typeList;

    /** @var Filesystem */
    private $fileSystem;

    /** @var Uploader */
    private $uploader;

    /**
     * @param Context $context
     * @param HeroBannerFactory $heroBannerFactory
     * @param HeroBannerRepository $heroBannerRepository
     * @param TypeListInterface $typeList
     * @param Filesystem $fileSystem
     * @param Uploader $uploader
     */
    public function __construct(
        Context $context,
        HeroBannerFactory $heroBannerFactory,
        HeroBannerRepository $heroBannerRepository,
        TypeListInterface $typeList,
        Filesystem $fileSystem,
        Uploader $uploader
    ) {
        parent::__construct($context);
        $this->heroBannerFactory = $heroBannerFactory;
        $this->heroBannerRepository = $heroBannerRepository;
        $this->typeList = $typeList;
        $this->fileSystem = $fileSystem;
        $this->uploader = $uploader;
    }

    /** @inheritDoc */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $bannerModel = $this->heroBannerFactory->create();
        $bannerId = isset($data['banner_id']) ? $data['banner_id'] : null;
        try {
            if ($bannerId) {
                $bannerModel = $this->heroBannerRepository->getById($bannerId);
            }
            $bannerModel->addData($data);
            $this->beforeSave($bannerModel);

            if ($bannerModel->getData(HeroBannerInterface::START_TIME) != '') {
                $startTime = date_create($bannerModel->getData(HeroBannerInterface::START_TIME));
                $startTime = date_format($startTime, "Y-m-d");
                $startTime = $startTime . " 00:00:00";
                $bannerModel->setStartTime($startTime);
            }

            if ($bannerModel->getData(HeroBannerInterface::END_TIME) == '') {
                $bannerModel->setEndTime(null);
            } else {
                $endTime = date_create($bannerModel->getData(HeroBannerInterface::END_TIME));
                $endTime = date_format($endTime, "Y-m-d");
                $endTime = $endTime . " 23:59:59";
                $bannerModel->setEndTime($endTime);
            }
            if ($this->getRequest()->getParam(HeroBannerInterface::BANNER_ID) == null) {
                if (array_key_exists(HeroBannerInterface::BANNER_DESKTOP_VIEW_IMAGE, $bannerModel->getData())
                    || array_key_exists(HeroBannerInterface::BANNER_VIDEO, $bannerModel->getData())
                    || array_key_exists(HeroBannerInterface::BANNER_YOUTUBE, $bannerModel->getData())
                ) {
                    $bannerModel->setData(HeroBannerInterface::CREATED_AT, date('y-m-d h:s:i'));
                    $bannerModel->setData(HeroBannerInterface::UPDATED_AT, date('y-m-d h:s:i'));
                    $bannerModel->setData(HeroBannerInterface::BANNER_TITLE, $data[HeroBannerInterface::BANNER_TITLE]);
                    $this->heroBannerRepository->save($bannerModel);
                    $this->messageManager->addSuccessMessage(__($bannerModel->getBannerTitle() . ' has been saved.'));
                } else {
                    $bannerModel->setData(HeroBannerInterface::BANNER_TITLE, $data[HeroBannerInterface::BANNER_TITLE]);
                    $this->messageManager->addErrorMessage(
                        __('Something went wrong while saving this ' . strtolower($bannerModel->getBannerTitle()) . ' banner.')
                    );
                }
            } else {
                $bannerModel->setData(HeroBannerInterface::BANNER_TITLE, $data[HeroBannerInterface::BANNER_TITLE]);
                $bannerModel->setData(HeroBannerInterface::UPDATED_AT, date('y-m-d h:s:i'));
                $this->heroBannerRepository->save($bannerModel);
                $this->messageManager->addSuccessMessage(
                    __($bannerModel->getBannerTitle() . ' has been saved.')
                );
            }

            $this->typeList->cleanType("full_page");
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', [HeroBannerInterface::BANNER_ID => $bannerModel->getBannerId()]);
            } else {
                $this->_redirect('*/*');
            }
            return;
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while saving this ' . strtolower($bannerModel->getBannerTitle()) . '.') . ' ' . $exception->getMessage()
            );
        }
        $this->_redirect('*/*/edit', [HeroBannerInterface::BANNER_ID => $bannerModel->getBannerId()]);
    }

    /**
     * Before save method
     *
     * @param HeroBanner $bannerModel
     * @return void
     */
    private function beforeSave($bannerModel)
    {
        $data = $bannerModel->getData();
        $bannerModel->setData($data);
        $mediaDirectory = $this->fileSystem->getDirectoryRead(
            DirectoryList::MEDIA
        );

        if ($data[HeroBannerInterface::BANNER_TYPE] == "image") {
            $imageField = HeroBannerInterface::BANNER_DESKTOP_VIEW_IMAGE;
            /* prepare banner image */
            if (isset($data[$imageField]) && isset($data[$imageField]['value'])) {
                if (isset($data[$imageField]['delete'])) {
                    unlink($mediaDirectory->getAbsolutePath() . $data[$imageField]['value']);
                    $bannerModel->setData($imageField, '');
                } else {
                    $bannerModel->setData($imageField, $data[$imageField]['value']);
                }
            }
            try {
                if (isset($data[$imageField])) {
                    if (isset($data[$imageField][0]['file'])) {
                        $this->uploader->moveFileFromTmp($data[$imageField][0]['file']);
                        $bannerModel->setData($imageField, $data[$imageField][0]['file']);
                    } else {
                        $bannerModel->setData($imageField, $data[$imageField][0]['name']);
                    }
                } else {
                    $bannerModel->setData($imageField, null);
                }
            } catch (Exception $exception) {
                if ($exception->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('Please Insert Image of types jpg, jpeg, gif, png')
                    );
                }
            }

            $imageField = HeroBannerInterface::BANNER_MOBILE_VIEW_IMAGE;
            if (isset($data[$imageField]) && isset($data[$imageField]['value'])) {
                if (isset($data[$imageField]['delete'])) {
                    unlink($mediaDirectory->getAbsolutePath() . $data[$imageField]['value']);
                    $bannerModel->setData($imageField, '');
                } else {
                    $bannerModel->setData($imageField, $data[$imageField]['value']);
                }
            }
            try {
                if (isset($data[$imageField])) {
                    if (isset($data[$imageField][0]['file'])) {
                        $this->uploader->moveFileFromTmp($data[$imageField][0]['file']);
                        $bannerModel->setData($imageField, $data[$imageField][0]['file']);
                    } else {
                        $bannerModel->setData($imageField, $data[$imageField][0]['name']);
                    }
                } else {
                    $bannerModel->setData($imageField, null);
                }
            } catch (Exception $exception) {
                if ($exception->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('Please Insert Image of types jpg, jpeg, gif, png')
                    );
                }
            }
        } else {
            $bannerModel->setData(HeroBannerInterface::BANNER_DESKTOP_VIEW_IMAGE, '')
                ->setData(HeroBannerInterface::BANNER_MOBILE_VIEW_IMAGE, '')
                ->setData(HeroBannerInterface::CALL_TO_ACTION, '')
                ->setData(HeroBannerInterface::BANNER_DESCRIPTION, '');
        }

        if ($data[HeroBannerInterface::BANNER_TYPE] == "video") {
            $videoField = HeroBannerInterface::BANNER_VIDEO;
            try {
                if (isset($data[$videoField])) {
                    if (isset($data[$videoField][0]['file'])) {
                        $this->uploader->moveFileFromTmp($data[$videoField][0]['file']);
                        $bannerModel->setData($videoField, $data[$videoField][0]['file']);
                    } else {
                        $bannerModel->setData($videoField, $data[$videoField][0]['name']);
                    }
                } else {
                    $bannerModel->setData($videoField, null);
                }
            } catch (Exception $exception) {
                if ($exception->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('Please Insert Video of type mp4')
                    );
                }
            }
            $imageField = HeroBannerInterface::BANNER_VIDEO_THUMB_IMAGE;
            /* prepare banner image */
            if (isset($data[$imageField]) && isset($data[$imageField]['value'])) {
                if (isset($data[$imageField]['delete'])) {
                    unlink($mediaDirectory->getAbsolutePath() . $data[$imageField]['value']);
                    $bannerModel->setData($imageField, '');
                } else {
                    $bannerModel->setData($imageField, $data[$imageField]['value']);
                }
            }
            try {
                if (isset($data[$imageField])) {
                    if (isset($data[$imageField][0]['file'])) {
                        $this->uploader->moveFileFromTmp($data[$imageField][0]['file']);
                        $bannerModel->setData($imageField, $data[$imageField][0]['file']);
                    } else {
                        $bannerModel->setData($imageField, $data[$imageField][0]['name']);
                    }
                } else {
                    $bannerModel->setData($imageField, null);
                }
            } catch (Exception $exception) {
                if ($exception->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('Please Insert Image of types jpg, jpeg, gif, png')
                    );
                }
            }
        } else {
            $bannerModel->setData(HeroBannerInterface::BANNER_VIDEO, '');
            $bannerModel->setData(HeroBannerInterface::BANNER_VIDEO_THUMB_IMAGE, '');
        }

        if ($data[HeroBannerInterface::BANNER_TYPE] != 'youtube') {
            $bannerModel->setData(HeroBannerInterface::BANNER_YOUTUBE, '');
        }
    }
}
