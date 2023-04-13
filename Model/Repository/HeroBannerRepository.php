<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model\Repository;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Api\HeroBannerRepositoryInterface;
use DeveloperHub\HeroBannerSlider\Model\HeroBannerFactory;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner as HeroBannerResource;

class HeroBannerRepository implements HeroBannerRepositoryInterface
{
    /** @var HeroBannerFactory */
    protected $heroBannerFactory;

    /** @var HeroBannerResource */
    protected $heroBannerResource;

    /**
     * @param HeroBannerFactory $heroBannerFactory
     * @param HeroBannerResource $heroBannerResource
     */
    public function __construct(
        HeroBannerFactory $heroBannerFactory,
        HeroBannerResource $heroBannerResource
    ) {
        $this->heroBannerFactory = $heroBannerFactory;
        $this->heroBannerResource = $heroBannerResource;
    }

    /** @inheritDoc */
    public function getById($bannerId)
    {
        $heroBanner = $this->heroBannerFactory->create();
        $this->heroBannerResource->load($heroBanner, $bannerId);
        if (!$heroBanner->getBannerId()) {
            throw new NoSuchEntityException(
                __('Hero banner with the "%1" ID wasn\'t found. Verify the ID and try again.', $bannerId)
            );
        }
        return $heroBanner;
    }

    /** @inheritDoc */
    public function deleteById($bannerId)
    {
        try {
            $heroBanner = $this->getById($bannerId);
            $this->delete($heroBanner);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('The stock item with the "%1" ID wasn\'t found. Verify the ID and try again.', $bannerId),
                $exception
            );
        }
        return true;
    }

    /** @inheritDoc */
    public function delete(HeroBannerInterface $heroBanner)
    {
        try {
            $this->heroBannerResource->delete($heroBanner);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Hero banner with the "%1" ID wasn\'t found. Verify the ID and try again.',
                    $heroBanner->getBannerId()
                ),
                $exception
            );
        }
        return true;
    }

    /** @inheritDoc */
    public function save(HeroBannerInterface $heroBanner)
    {
        try {
            if ($heroBanner->getBannerId()) {
                $heroBanner = $this->getById($heroBanner->getBannerId())
                    ->addData($heroBanner->getData());
            }
            $this->heroBannerResource->save($heroBanner);
        } catch (\Exception $exception) {
            if ($heroBanner->getBannerId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save Hero Banner with ID %1. Error: %2',
                        [$heroBanner->getBannerId(), $exception->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(
                __('Unable to save new Hero Banner. Error: %1', $exception->getMessage())
            );
        }
        return $heroBanner;
    }
}
