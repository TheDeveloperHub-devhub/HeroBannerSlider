<?php declare(strict_types=1);

namespace DeveloperHub\HeroBannerSlider\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use DeveloperHub\HeroBannerSlider\Model\Uploader;

class Upload extends Action
{
    /** @var Uploader */
    private $uploader;

    /**
     * @param Context $context
     * @param Uploader $uploader
     */
    public function __construct(
        Context $context,
        Uploader $uploader
    ) {
        parent::__construct($context);
        $this->uploader = $uploader;
    }

    /** @return ResponseInterface|Json|ResultInterface */
    public function execute()
    {
        try {
            $postData = $this->getRequest()->getParam('param_name');
            $result = $this->uploader->saveFileToTmpDir($postData);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /** @return string */
    public function getFieldName()
    {
        return $this->_request->getParam('field');
    }
}
