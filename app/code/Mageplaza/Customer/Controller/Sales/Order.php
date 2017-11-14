<?php
namespace Mageplaza\Customer\Controller\Sales;

class Order extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Your Orders')); //for browser tab title
        $this->_view->getLayout()->getBlock('page.main.title')->setPageTitle(__('Yours Orders')); //dynamic user name as a title
        return $resultPage;
	}
}