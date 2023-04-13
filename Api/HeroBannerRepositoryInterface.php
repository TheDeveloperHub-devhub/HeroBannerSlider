<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;

interface HeroBannerRepositoryInterface
{
    /**
     * Get Banner by ID.
     *
     * @param int $bannerId
     * @return HeroBannerInterface
     * @throws NoSuchEntityException If $bannerId is not found
     * @throws LocalizedException
     */
    public function getById($bannerId);

    /**
     * Delete Banner by ID.
     * @param int $bannerId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($bannerId);

    /**
     * Delete Banner item
     *
     * @param HeroBannerInterface $heroBanner
     * @return bool
     * @throws LocalizedException
     */
    public function delete(HeroBannerInterface $heroBanner);

    /**
     * Save Hero Banner.
     *
     * @param HeroBannerInterface $heroBanner
     * @return HeroBannerInterface
     * @throws CouldNotSaveException
     */
    public function save(HeroBannerInterface $heroBanner);
}
