<?php 

namespace Mageplaza\SocialLogin\Controller\Index;
 
 
class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
	    $mobile = $this->getRequest()->getPost('mobile');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
        $sql = "Select value FROM customer_entity_text where attribute_id=155 AND value=".$mobile;
        $result = $connection->fetchAll($sql);
		if(empty($result)){
		 echo "no";
		}else{
		echo "yes";
		}
    }
}
?>