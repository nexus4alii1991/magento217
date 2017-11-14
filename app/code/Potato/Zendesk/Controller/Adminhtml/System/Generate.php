<?php

namespace Potato\Zendesk\Controller\Adminhtml\System;

use Magento\Backend\App\Action;
use Potato\Zendesk\Model\Authorization;

/**
 * Class Generate
 */
class Generate extends Action
{
    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * Generate constructor.
     * @param Action\Context $context
     * @param Authorization $authorization
     */
    public function __construct(
        Action\Context $context,
        Authorization $authorization
    ) {
        parent::__construct($context);
        $this->authorization = $authorization;
    }
    
    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $this->authorization->createToken();
            $this->messageManager->addSuccessMessage(__('Token successfully created.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('adminhtml/system_config/edit/section/potato_zendesk');
    }
}