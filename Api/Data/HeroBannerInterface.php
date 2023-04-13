<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Api\Data;

interface HeroBannerInterface
{
    const BANNER_ID = 'banner_id';
    const BANNER_TITLE = 'banner_title';
    const BANNER_DESCRIPTION = 'banner_description';
    const BANNER_TYPE = 'banner_type';
    const BANNER_DESKTOP_VIEW_IMAGE = 'banner_desktop_view_image';
    const BANNER_MOBILE_VIEW_IMAGE = "banner_mobile_view_image";
    const BANNER_VIDEO = 'banner_video';
    const BANNER_VIDEO_THUMB_IMAGE = 'banner_video_thumb_image';
    const BANNER_YOUTUBE = 'banner_youtube';
    const BANNER_VIDEO_AUTOPLAY = 'banner_video_autoplay';
    const BANNER_VIMEO = 'banner_vimeo';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    const LABEL_BUTTON_TEXT = 'label_button_text';
    const CALL_TO_ACTION = 'call_to_action';
    const POSITION = 'position';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /** @return int|null */
    public function getBannerId();

    /**
     * @param int $bannerId
     * @return HeroBannerInterface
     */
    public function setBannerId($bannerId);

    /** @return string */
    public function getBannerTitle();

    /**
     * @param string $bannerTitle
     * @return HeroBannerInterface
     */
    public function setBannerTitle($bannerTitle);

    /** @return string */
    public function getBannerDescription();

    /**
     * @param string $bannerDescription
     * @return HeroBannerInterface
     */
    public function setBannerDescription($bannerDescription);

    /** @return string */
    public function getBannerType();

    /**
     * @param string $bannerType
     * @return HeroBannerInterface
     */
    public function setBannerType($bannerType);

    /** @return string */
    public function getBannerDesktopViewImage();

    /**
     * @param string $bannerDesktopViewImage
     * @return HeroBannerInterface
     */
    public function setBannerDesktopViewImage($bannerDesktopViewImage);

    /** @return string */
    public function getBannerMobileViewImage();

    /**
     * @param string $bannerMobileViewImage
     * @return HeroBannerInterface
     */
    public function setBannerMobileViewImage($bannerMobileViewImage);

    /** @return string */
    public function getBannerVideo();

    /**
     * @param string $bannerVideo
     * @return HeroBannerInterface
     */
    public function setBannerVideo($bannerVideo);

    /** @return string */
    public function getBannerVideoThumbImage();

    /**
     * @param string $bannerVideoThumbImage
     * @return HeroBannerInterface
     */
    public function setBannerVideoThumbImage($bannerVideoThumbImage);

    /** @return string */
    public function getBannerYoutube();

    /**
     * @param string $bannerYoutube
     * @return HeroBannerInterface
     */
    public function setBannerYoutube($bannerYoutube);

    /** @return string */
    public function getBannerVideoAutoplay();

    /**
     * @param string $bannerVideoAutoplay
     * @return HeroBannerInterface
     */
    public function setBannerVideoAutoplay($bannerVideoAutoplay);

    /** @return string */
    public function getBannerVimeo();

    /**
     * @param string $bannerVimeo
     * @return HeroBannerInterface
     */
    public function setBannerVimeo($bannerVimeo);

    /** @return string|null */
    public function getStartTime();

    /**
     * @param string $startTime
     * @return HeroBannerInterface
     */
    public function setStartTime($startTime);

    /** @return string|null */
    public function getEndTime();

    /**
     * @param string $endTime
     * @return HeroBannerInterface
     */
    public function setEndTime($endTime);

    /** @return string */
    public function getLabelButtonText();

    /**
     * @param string $labelButtonText
     * @return HeroBannerInterface
     */
    public function setLabelButtonText($labelButtonText);

    /** @return string */
    public function getCallToAction();

    /**
     * @param string $callToAction
     * @return HeroBannerInterface
     */
    public function setCallToAction($callToAction);

    /** @return int|null */
    public function getPosition();

    /**
     * @param int|null $position
     * @return HeroBannerInterface
     */
    public function setPosition($position);

    /** @return bool|null */
    public function getIsActive();

    /**
     * @param int|bool $isActive
     * @return HeroBannerInterface
     */
    public function setIsActive($isActive);

    /** @return string|null */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return HeroBannerInterface
     */
    public function setCreatedAt($createdAt);

    /** @return string|null */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return HeroBannerInterface
     */
    public function setUpdatedAt($updatedAt);
}
