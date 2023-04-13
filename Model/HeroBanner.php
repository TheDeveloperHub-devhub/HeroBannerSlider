<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use DeveloperHub\HeroBannerSlider\Api\Data\HeroBannerInterface;
use DeveloperHub\HeroBannerSlider\Model\ResourceModel\HeroBanner as HeroBannerResource;

class HeroBanner extends AbstractModel implements HeroBannerInterface, IdentityInterface
{
    /**#@+
     * Banner's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /** CMS page cache tag */
    const CACHE_TAG = 'developerhub_herobanner';

    /** Cache tag */
    protected $cacheTag = 'developerhub_herobanner';

    /** Prefix of model banner names */
    protected $eventPrefix = 'developerhub_herobanner';

    /** Initialize resource model */
    protected function _construct()
    {
        $this->_init(HeroBannerResource::class);
    }

    /** @return array */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /** @inheritDoc */
    public function getBannerId()
    {
        return $this->getData(self::BANNER_ID);
    }

    /** @inheritDoc */
    public function setBannerId($bannerId)
    {
        return $this->setData(self::BANNER_ID, $bannerId);
    }

    /** @inheritDoc */
    public function getBannerTitle()
    {
        return $this->getData(self::BANNER_TITLE);
    }

    /** @inheritDoc */
    public function setBannerTitle($bannerTitle)
    {
        return $this->setData(self::BANNER_TITLE, $bannerTitle);
    }

    /** @inheritDoc */
    public function getBannerDescription()
    {
        return $this->getData(self::BANNER_DESCRIPTION);
    }

    /** @inheritDoc */
    public function setBannerDescription($bannerDescription)
    {
        return $this->setData(self::BANNER_DESCRIPTION, $bannerDescription);
    }

    /** @inheritDoc */
    public function getBannerType()
    {
        return $this->getData(self::BANNER_TYPE);
    }

    /** @inheritDoc */
    public function setBannerType($bannerType)
    {
        return $this->setData(self::BANNER_TYPE, $bannerType);
    }

    /** @inheritDoc */
    public function getBannerDesktopViewImage()
    {
        return $this->getData(self::BANNER_DESKTOP_VIEW_IMAGE);
    }

    /** @inheritDoc */
    public function setBannerDesktopViewImage($bannerDesktopViewImage)
    {
        return $this->setData(self::BANNER_DESKTOP_VIEW_IMAGE, $bannerDesktopViewImage);
    }

    /** @inheritDoc */
    public function getBannerMobileViewImage()
    {
        return $this->getData(self::BANNER_MOBILE_VIEW_IMAGE);
    }

    /** @inheritDoc */
    public function setBannerMobileViewImage($bannerMobileViewImage)
    {
        return $this->setData(self::BANNER_MOBILE_VIEW_IMAGE, $bannerMobileViewImage);
    }

    /** @inheritDoc */
    public function getBannerVideo()
    {
        return $this->getData(self::BANNER_VIDEO);
    }

    /** @inheritDoc */
    public function setBannerVideo($bannerVideo)
    {
        return $this->setData(self::BANNER_VIDEO, $bannerVideo);
    }

    /** @inheritDoc */
    public function getBannerVideoThumbImage()
    {
        return $this->getData(self::BANNER_VIDEO_THUMB_IMAGE);
    }

    /** @inheritDoc */
    public function setBannerVideoThumbImage($bannerVideoThumbImage)
    {
        return $this->setData(self::BANNER_VIDEO_THUMB_IMAGE, $bannerVideoThumbImage);
    }

    /** @inheritDoc */
    public function getBannerYoutube()
    {
        return $this->getData(self::BANNER_YOUTUBE);
    }

    /** @inheritDoc */
    public function setBannerYoutube($bannerYoutube)
    {
        return $this->setData(self::BANNER_YOUTUBE, $bannerYoutube);
    }

    /** @inheritDoc */
    public function getBannerVideoAutoplay()
    {
        return $this->getData(self::BANNER_VIDEO_AUTOPLAY);
    }

    /** @inheritDoc */
    public function setBannerVideoAutoplay($bannerVideoAutoplay)
    {
        return $this->setData(self::BANNER_VIDEO_AUTOPLAY, $bannerVideoAutoplay);
    }

    /** @inheritDoc */
    public function getBannerVimeo()
    {
        return $this->getData(self::BANNER_VIMEO);
    }

    /** @inheritDoc */
    public function setBannerVimeo($bannerVimeo)
    {
        return $this->setData(self::BANNER_VIMEO, $bannerVimeo);
    }

    /** @inheritDoc */
    public function getStartTime()
    {
        return $this->getData(self::START_TIME);
    }

    /** @inheritDoc */
    public function setStartTime($startTime)
    {
        return $this->setData(self::START_TIME, $startTime);
    }

    /** @inheritDoc */
    public function getEndTime()
    {
        return $this->getData(self::END_TIME);
    }

    /** @inheritDoc */
    public function setEndTime($endDate)
    {
        return $this->setData(self::END_TIME, $endDate);
    }

    /** @inheritDoc */
    public function getLabelButtonText()
    {
        return $this->getData(self::LABEL_BUTTON_TEXT);
    }

    /** @inheritDoc */
    public function setLabelButtonText($labelButtonText)
    {
        return $this->setData(self::LABEL_BUTTON_TEXT, $labelButtonText);
    }

    /** @inheritDoc */
    public function getCallToAction()
    {
        return $this->getData(self::CALL_TO_ACTION);
    }

    /** @inheritDoc */
    public function setCallToAction($callToAction)
    {
        return $this->setData(self::CALL_TO_ACTION, $callToAction);
    }

    /** @inheritDoc */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /** @inheritDoc */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /** @inheritDoc */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /** @inheritDoc */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /** @inheritDoc */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /** @inheritDoc */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /** @inheritDoc */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /** @inheritDoc */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /** @return array|mixed|null */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /** @return array */
    public function getCustomerGroup()
    {
        return (array)$this->getData('customer_group_id');
    }
}
