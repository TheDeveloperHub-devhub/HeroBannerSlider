<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Uploader
{
    /** @var Database */
    public $coreFileStorageDatabase;

    /** @var WriteInterface */
    public $mediaDirectory;

    /** @var UploaderFactory */
    private $uploaderFactory;

    /** @var StoreManagerInterface */
    public $storeManager;

    /** @var LoggerInterface */
    public $logger;

    /** @var string */
    public $baseTmpPath;

    /** @var string */
    public $basePath;

    /** @var string */
    public $allowedExtensions;

    /**
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param $baseTmpPath
     * @param $basePath
     * @param $allowedExtensions
     * @throws FileSystemException
     */
    public function __construct(
        Database              $coreFileStorageDatabase,
        Filesystem            $filesystem,
        UploaderFactory       $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface       $logger,
        $baseTmpPath,
        $basePath,
        $allowedExtensions = []
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = $baseTmpPath;
        $this->basePath = $basePath;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @param $baseTmpPath
     * @return void
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * @param $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param $allowedExtensions
     * @return void
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /** @return string */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    /** @return string */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /** @return array|mixed|string */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * @param $path
     * @param $name
     * @return string
     */
    public function getFilePath($path, $name)
    {
        return rtrim($path, '/') . '/' . ltrim($name, '/');
    }

    /**
     * @param $name
     * @return mixed
     * @throws LocalizedException
     */
    public function moveFileFromTmp($name)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();

        $baseFilePath = $this->getFilePath($basePath, $name);
        $baseTmpFilePath = $this->getFilePath($baseTmpPath, $name);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpFilePath,
                $baseFilePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpFilePath,
                $baseFilePath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $name;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
    }

    /**
     * @param $fileId
     * @return mixed
     * @throws LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);

        if (count($this->getAllowedExtensions())) {
            $uploader->setAllowedExtensions($this->getAllowedExtensions());
        }

        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));

        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }
        /**
         * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->getBaseUrl() . $this->getFilePath($baseTmpPath, $result['file']);

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }

    /**
     * @param $input
     * @param $data
     * @return mixed|string
     */
    public function uploadFileAndGetName($input, $data)
    {
        if (!isset($data[$input])) {
            return '';
        }
        if (is_array($data[$input]) && !empty($data[$input]['delete'])) {
            return '';
        }

        if (isset($data[$input][0]['name']) && isset($data[$input][0]['tmp_name'])) {
            try {
                $result = $this->moveFileFromTmp($data[$input][0]['file']);
                return $result;
            } catch (\Exception $e) {
                return '';
            }
        } elseif (isset($data[$input][0]['name'])) {
            return $data[$input][0]['name'];
        }
        return '';
    }
}
