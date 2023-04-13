<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\HeroBanner;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\HeroBannerFactory;
use DeveloperHub\HeroBannerSlider\Model\Repository\HeroBannerRepository;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner\CollectionFactory;
use DeveloperHub\HeroBannerSlider\Model\Uploader;

class DataProvider extends ModifierPoolDataProvider
{
    /**#@+
     * Base media path for banner's images and videos
     */
    const FILE_TMP_PATH = 'developerhub_herobanner/tmp/slider/file';
    const FILE_PATH = 'developerhub_herobanner/slider/file';
    /**#@-*/

    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /** @var array */
    protected $loadedData;

    /** @var RequestInterface */
    private $request;

    /** @var HeroBannerRepository */
    private $heroBannerRepository;

    /** @var HeroBannerFactory */
    private $heroBannerFactory;

    /** @var Uploader */
    private $uploader;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Uploader $uploader
     * @param RequestInterface $request
     * @param HeroBannerFactory $heroBannerFactory
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param HeroBannerRepository $heroBannerRepository
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Uploader $uploader,
        RequestInterface $request,
        HeroBannerFactory $heroBannerFactory,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        HeroBannerRepository $heroBannerRepository,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data,
            $pool
        );
        $this->uploader = $uploader;
        $this->request = $request;
        $this->heroBannerFactory = $heroBannerFactory;
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->heroBannerRepository = $heroBannerRepository;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $heroBanner = $this->getCurrentHeroBanner();
        $this->loadedData[$heroBanner->getBannerId()] = $heroBanner->getData();
        if ($heroBanner->getBannerVideoThumbImage() != null) {
            $image = [];
            $image[0]['name'] = $heroBanner->getBannerVideoThumbImage();
            $image[0]['url'] = $this->getFileUrl($image[0]['name']);
            $this->loadedData[$heroBanner->getBannerId()][HeroBannerInterface::BANNER_VIDEO_THUMB_IMAGE] = $image;
        }
        if ($heroBanner->getBannerVideo() != null) {
            $video = [];
            $video[0]['name'] = $heroBanner->getBannerVideo();
            $video[0]['url'] = $this->getFileUrl($video[0]['name']);
            $this->loadedData[$heroBanner->getBannerId()][HeroBannerInterface::BANNER_VIDEO] = $video;
        }
        if ($heroBanner->getBannerDesktopViewImage() != null) {
            $image = [];
            $image[0]['name'] = $heroBanner->getBannerDesktopViewImage();
            $image[0]['url'] = $this->getFileUrl($image[0]['name']);
            $this->loadedData[$heroBanner->getBannerId()][HeroBannerInterface::BANNER_DESKTOP_VIEW_IMAGE] = $image;
        }
        if ($heroBanner->getBannerMobileViewImage() != null) {
            $image = [];
            $image[0]['name'] = $heroBanner->getBannerMobileViewImage();
            $image[0]['url'] = $this->getFileUrl($image[0]['name']);
            $this->loadedData[$heroBanner->getBannerId()][HeroBannerInterface::BANNER_MOBILE_VIEW_IMAGE] = $image;
        }
        return $this->loadedData;
    }

    /** @return HeroBannerInterface */
    private function getCurrentHeroBanner(): HeroBannerInterface
    {
        $bannerId = (int) $this->request->getParam($this->getRequestFieldName());
        if ($bannerId) {
            try {
                $heroBanner = $this->heroBannerRepository->getById($bannerId);
            } catch (LocalizedException $exception) {
                $heroBanner = $this->heroBannerFactory->create();
            }
            return $heroBanner;
        }

        $data = $this->dataPersistor->get('developerhub_hero_banner');
        if (empty($data)) {
            return $this->heroBannerFactory->create();
        }
        $this->dataPersistor->clear('developerhub_hero_banner');

        return $this->heroBannerFactory->create()
            ->setData($data);
    }

    /**
     * @param $filePath
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getFileUrl($filePath): string
    {
        if ($filePath && is_string($filePath)) {
            $url = $this->uploader->getBaseUrl() . self::FILE_PATH . DIRECTORY_SEPARATOR . $filePath;
        } else {
            throw new LocalizedException(
                __('Something went wrong while getting the image url.')
            );
        }
        return $url;
    }
}
