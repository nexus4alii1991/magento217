<?php
namespace TW\Categorylist\Controller\Adminhtml\Category\Thumbnailimage;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload extends \Magento\Backend\App\Action
{
    protected $baseTmpPath;
    protected $imageUploader;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        parent::__construct($context);

    }
    public function execute() {

        try {
            $result = $this->imageUploader->saveFileToTmpDir('thumbnail');
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug("ImageUploaderError");
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
			\Magento\Framework\App\ObjectManager::getInstance()
				->get('Psr\Log\LoggerInterface')
				->debug("ImageUploader Error".$e);
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}